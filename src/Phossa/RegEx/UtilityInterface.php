<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\RegEx;

/**
 * RegEx related utility interface
 *
 * @interface
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface UtilityInterface
{
    /**
     * Convert regex to PCRE pattern by adding delimiter and modifiers
     *
     * @param  string $regex the regex
     * @param  string $modifiers (optional) pattern modifiers\
     * @param  string $delimiters (optional) pick delimiter char from here
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function toPattern(
        /*# string */ $regex,
        /*# string */ $modifiers  = '',
        /*# string */ $delimiters = '/#@%~&!'
    )/*# : string */;

    /**
     * Check regex for syntax error. return '' or error message
     *
     * @param  string $regex the regex
     * @return string the error message or empty string
     * @access public
     * @static
     * @api
     */
    public static function validateRegEx(
        /*# string */ $regex
    )/*# : string */;

    /**
     * Modify the resulting regex
     *
     * Convert to pattern (with pattern modifier), or group regex (with named
     * group), or add boundary, or add anchor
     *
     * @param  string $regex input regular expression
     * @param  bool|string $pattern_modifier return pattern instead of regex
     * @param  bool|string $named_group add named group to regex
     * @param  bool $boundary add default boundary
     * @param  bool $anchor add start/end anchor
     * @return string
     * @see    self::toPattern()
     * @see    self::addBoundary()
     * @see    self::groupRegEx()
     * @access public
     * @static
     * @api
     */
    public static function modifyRegEx(
        /*# string */ $regex,
        $pattern_modifier  = true,
        $named_group       = false,
        $boundary          = false,
        $anchor            = false
    )/*# : string */;

    /**
     * Escaped those unescaped chars in the string
     *
     * @param  string $string string to modify
     * @param  string $char unescaped CHAR to look for
     * @param  string $escape the escape char to use
     * @param  int $option (optional) regex option
     * @return string
     * @access public
     * @api
     */
    public function escapeUnescaped(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# string */;

    /**
     * Unescaped those escaped char in the string
     *
     * @param  string $string string to modify
     * @param  string $char unescaped char to look for
     * @param  string $escape the escape char to use
     * @param  int $option (optional) regex option
     * @return string
     * @access public
     * @api
     */
    public function unEscapeEscaped(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# string */;

    /**
     * Test if string is wrapped with char
     *
     * @param  string $string the subject string
     * @param  string $char the literal wrapping char
     * @param  string $escape (optional) the literal escape char
     * @param  int $option (optional) regex option
     * @return bool
     * @access public
     * @static
     * @api
     */
    public static function isWrappedWithChar(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : bool */;

    /**
     * Wrap a string with a char. Usualy with a quote char.
     *
     * This method also escape the unescaped same char in the string with a
     * single '\' in the string and escaped same char with a double of '\\'
     *
     * @param  string $string the subject string
     * @param  string $char the literal wrapping char
     * @param  string $escape (optional) the literal escape char
     * @param  int $option (optional) regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function wrapWithChar(
        /*# string */ $string,
        /*# string */ $char   = '"',
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Unwrap a string with char. Used to remove surrounding quotes etc.
     *
     * If not meet requirements return the same input $string
     *
     * @param  string $string the target string
     * @param  string $char the literal char to unwrap
     * @param  string $escape (optional) the escape char
     * @param  int $option (optional) regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function unWrapWithChar(
        /*# string */ $string,
        /*# string */ $char   = '"',
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Wrap regex with group OR named group
     *
     * @param  string $regex the regex
     * @param  string $name (optional) group name
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function groupRegEx(
        /*# string */ $regex,
        /*# string */ $name = ''
    )/*# : string */;

    /**
     * Wrap regex with matching boundary
     *
     * default boundaries for numerical regex stuff
     *
     *      begin: (?<=\b|[^\d])
     *      end  : (?=\b|[^\d])
     *
     * @param  string $regex the regex
     * @param  string $begin the begin boundary pattern
     * @param  string $end the end boundary pattern
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function addBoundary(
        /*# string */ $regex,
        /*# string */ $begin = '(?<=\b|[^\d])',
        /*# string */ $end   = '(?=\b|[^\d])'
    )/*# : string */;

    /**
     * Get the line and col number from the offset in the string
     *
     * @param  string &$string the target string
     * @param  int $offset the offset (start from 0)
     * @param  bool $startFromOne return line/col start from one
     * @return array list($line, $col) (start from 1)
     * @access public
     * @static
     * @api
     */
    public static function getLineColumn(
        /*# string */ &$string,
        /*# int */ $offset,
        /*# bool */ $startFromOne = true
    )/*# : array */;
}
