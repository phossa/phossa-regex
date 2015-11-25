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
     * @param  string $delimiter (optional) pick delimiter char from here
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function toPattern(
        /*# string */ $regex,
        /*# string */ $modifiers = '',
        /*# string */ $delimiter = '/#@%~&!'
    )/*# : string */;

    /**
     * Check regex for syntax error. return '' or error message
     *
     * @param  string $regex the regex
     * @return string the error message
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
     * @access public
     * @static
     * @api
     */
    public static function modifyRegEx(
        /*# string */ $regex,
        $pattern_modifier  = false,
        $named_group       = false,
        $boundary          = false,
        $anchor            = false
    )/*# : string */;

    /**
     * Wrap a string with a char. Usualy a quote.
     *
     * This method also escape the unescaped same char in the string with a
     * single '\' in the string and escaped same char with a double of '\\'
     *
     * @param  string $string the subject string
     * @param  string $char the literal wrapping char
     * @param  string $escape (optional) the literal escape char
     * @return string
     * @access public
     * @static
     */
    public static function wrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\'
    )/*# : string */;

    /**
     * Unwrap a string with char. Used to remove surrounding quotes etc.
     *
     * @param  string $string the target string
     * @param  string $char the literal char to unwrap
     * @param  string $escape (optional) the escape char
     * @return string
     * @access public
     * @static
     */
    public static function unwrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\'
    )/*# : string */;

    /**
     * Wrap regex with group OR named group
     *
     * @param  string $regex the regex
     * @param  string $name (optional) group name
     * @return string
     * @access public
     * @static
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
     */
    public static function addBoundary(
        /*# string */ $regex,
        /*# string */ $begin = '(?<=\b|[^\d])',
        /*# string */ $end = '(?=\b|[^\d])'
    )/*# : string */;
}
