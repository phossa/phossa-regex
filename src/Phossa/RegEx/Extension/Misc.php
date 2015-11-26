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

/**
 * Misc regex
 *
 * @static
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Misc extends StaticAbstract
{
    /**
     * Return regex to match PHP-like variable name
     *
     * regex:   [a-zA-Z_\x7f-\xff][\w\x7f-\xff]*
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function variableName()/*# : string */
    {
        return '[a-zA-Z_\x7f-\xff][\w\x7f-\xff]*';
    }

    /**
     * Return regex to match csv fields, loosely base on RFC 4180
     *
     * Newline allowed in double quoted fields. Double quote in the quoted
     * field is escaped by another double quote. Backslash is a normal char.
     *
     * e.g. test,"s p a c e","""double"" quotes",,",,"
     *
     * @param  string $char char to seperate fields
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     */
    public static function csvField(
        /*# string */ $char = ',',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        $quoted = sprintf(
            '"%s*+"',
            RegEx::charWithExclusion(['"'], ['""'], $option)
        );

        // quoted | non-quoted | empty
        return sprintf('(?<=%s|^)\s*+(?:%s|[^%s"]*+)(?=%s|\Z)',
            $char, $quoted, $char, $char
        );
    }

    /**
     * Return regex to match comma seperated strings
     *
     * comma in quotes are ignored. the unrolling pattern implemented
     *
     * @param  string $char the seperate char, default ','
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     */
    public static function commaField(
        /*# string */ $char = ',',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        $quoted = RegEx::quotedString($option);
        return sprintf(
            '(?<=%s|^)[^%s"\']*(?:%s[^%s"\']*)*(?=%s|\Z)',
            $char, $char, $quoted, $char, $char
        );
    }
}
