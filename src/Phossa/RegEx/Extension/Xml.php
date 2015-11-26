<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\RegEx\Extension;

use Phossa\Shared\Pattern\StaticAbstract;
use Phossa\RegEx\RegEx;
use Phossa\RegEx\RegExOption;
use Phossa\RegEx\Utility as Util;

/**
 * XML related regex
 *
 * @static
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Xml extends StaticAbstract
{
    /**#@+
     * Different type of XML tag type
     *
     * @access  public
     * @const
     */
    const XML_TAG_OPEN      = 1;
    const XML_TAG_CLOSE     = 2;
    const XML_TAG_SELFCLOSE = 4;
    const XML_TAG_ALL       = 7;
    /**#@-*/

    /**
     * Return regex to match XML tag name (no unicode stuff)
     *
     * regex:   [_:A-Za-z][-.:\w]*+
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function xmlName()/*# : string */
    {
        return '[_:A-Za-z][-.:\w]*+';
    }

    /**
     * Return regex to match XML/HTML generic attributes part (loose)
     *
     * Not exactly follow REC-xml-19980210, Attribute ::= Name Eq AttValue
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function xmlAttribute()/*# : string */
    {
        $excl = array('/>', '/', '>', '<');
        return RegEx::stringWithExclusion($excl);
    }

    /**
     * Return regex to match xml attributes' name and value pair
     *
     * @param  string $name named group for name part
     * @param  string $value named group for value part
     * @return string
     * @access public
     * @static
     */
    public static function xmlNameValue($name = '', $value = '')/*# : string */
    {
        return sprintf('%s(?:\s*+=\s*+%s)?',
            Util::groupRegEx(static::xmlName(), $name),
            Util::groupRegEx(RegEx::quotedString(), $value)
        );
    }

    /**
     * Return regex to match all type of XML tag
     *
     * match open, close tags and also self-close tags
     *
     * regex:
     *      <                       # openning <
     *        (/)?                  # closing tag?
     *        (?<name>tagname)      # tag with name
     *        (?<attr>attr)|\s*+    # attribute with name
     *        (/)?                  # self-close
     *      >                       # closing >
     *
     * @param  int $type tag type, self::XML_TAG_ALL etc.
     * @param  string $tagname optional named group for tag
     * @param  string $attrname optional named group for attributes
     * @return string
     * @access public
     * @static
     */
    public static function xmlTag(
        /*# int */ $type        = self::XML_TAG_ALL,
        /*# string */ $tagname  = '',
        /*# string */ $attrname = ''
    )/*# : string */ {
        // xml parts
        $tag  = Util::groupRegEx(static::xmlName(), $tagname);
        $attr = Util::groupRegEx(static::xmlAttribute(), $attrname);

        // regex
        switch ($type) {
            case self::XML_TAG_OPEN :
                $regex = sprintf('<%s%s>', $tag, $attr);
                break;
            case self::XML_TAG_CLOSE :
                $regex = sprintf('</%s\s*+>', $tag);
                break;
            case self::XML_TAG_SELFCLOSE :
                $regex = sprintf('<%s%s/>', $tag, $attr);
                break;
            default :
                $ref = $tagname ? '-2' : '-1';
                $regex = sprintf('<(/)?%s(?(%s)\s*+|%s(/)?)>',
                    $tag, $ref, $attr);
                break;
        }
        return $regex;
    }

    /**
     * Return regex to match xml/html comment
     *
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     */
    public static function xmlComment(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        return RegEx::stringWithOpenClose('<!--', '-->', $option);
    }

    /**
     * Return regex to match XML CDATA
     *
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     */
    public static function xmlCdata(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        return RegEx::stringWithOpenClose('<![CDATA[', ']]>', $option);
    }

    /**
     * Return regex to match xml (p)rocessing (i)nstructions
     *
     * Quoted substring and heredoc type of substrings are allowed in PI. Key
     * is the $option setting.
     *
     * @param  string $target literal target, like 'php'
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     */
    public static function xmlProcessing(
        /*# string */ $target = 'php',
        /*# int */ $option = RegExOption::OPTION_ALLOPTS
    )/*# : string */ {
        return RegEx::stringWithOpenClose('<?' . $target, '?>', $option);
    }

    /**
     * Return regex to match xml declaration
     *
     * @param  int $option regex options
     * @return string
     * @access public
     * @static
     */
    public static function xmlDeclaration(
        /*# int */ $option = RegExOption::OPTION_ALLOPTS
    )/*# : string */ {
        if ($option & RegExOption::OPTION_CLOSURE) {
            $end = '\?\>';
        } else {
            $end = '(?:\?\>|\Z)';
        }
        return sprintf('(?s:\<\![A-Z].*%s)', $end);
    }
}
