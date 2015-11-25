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
 * RegExInterface
 *
 * Construct basic and common regular expressions
 *
 * @interface
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface RegExInterface
{
    /**
     * Return regex to match ANY SINGLE char WITH some exclusions
     *
     * $exclude:        array('X','YYY') to exclude 'X' and 'YYY'.
     * $allowed:        array of allowed regex.
     *
     * @param  array $exclude array of literal OR regex to exclude.
     * @param  array $allowed array of allowed REGEX
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function charWithExclusion(
        array $exclude = [],
        array $allowed = [],
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match a single quoted string
     *
     * @param  int $option regex option
     * @param  string $quoteCharacter literal or regex
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function singleQuotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT,
        /*# string */ $quoteCharacter = "'"
    )/*# : string */;

    /**
     * Return regex to match a double quoted string
     *
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function doubleQuotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match single or double quoted string.
     *
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function quotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match unescaped char (0 or even backslashes).
     *
     * The result regex can not be used with preg_replace* due to the '\K'
     * in the regex, which may cause bugs
     *
     * regex:
     *    (?<!\\\\)CHAR                         # the CHAR with no '\' precede
     *    |                                     # OR
     *    (?=(?:\\\\\\\\)++CHAR)[\\\\]++\KCHAR  # the CHAR with even '\' precede
     *
     * @param  string $char the CHAR, or REGEX (unset OPTION_LITERAL first)
     * @param  int $option regex option
     * @param  string $escape the escape char, normally backslash
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function unEscapedChar(
        /*# string */ $char,
        /*# int */ $option = RegExOption::OPTION_DEFAULT,
        /*# string */ $escape = '\\'
    )/*# : string */;

    /**
     * Return regex to match escaped char (with odd number of backslashes)
     *
     * The result regex can not be used with preg_replace* due to the '\K'
     * in the regex, which may cause bugs
     *
     * @param  string $char the CHAR, or REGEX (unset OPTION_LITERAL first)
     * @param  int $option regex option
     * @param  string $escape the escape char, normally backslash
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function escapedChar(
        /*# string */ $char,
        /*# int */ $option = self::OPTION_DEFAULT,
        /*# string */ $escape = '\\'
    )/*# : string */;

    /**
     * Return regex to match an open/close structure, such as C comment
     *
     * e.g. regex to find template comment
     *
     * ```php
     * $regex = RegEx::stringWithOpenClose('{*', '*}');
     * ```
     *
     * @param  string $open literal or regex
     * @param  string $close literal or regex
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function stringWithOpenClose(
        /*# string */ $open,
        /*# string */ $close,
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match heredoc/nowdoc type of structure
     *
     * regex:
     *      (?sm:                       # dot-for-all & multiple line mode
     *        <<<                       # left most
     *          ([\'])?                 # nowdoc ?
     *            (\w++)                # symbol
     *          (?(-2)\')               # complete nowdoc
     *          .+?                     # lazy match anything
     *        ^\g{-1}                   # close heredoc/nowdoc
     *         \s*+;?\s*+$              # optional ';' at the same line
     *
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function hereDoc(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match string without certain char/string (exclusion)
     *
     * - Quoted substring may be allowed (OPTION_QUOTEOK).
     * - Heredoc substring may be allowed (OPTION_HEREDOC).
     * - Exclusions are allowed in the quoted substring and heredoc.
     * - May allow newline (OPTION_MULTILINE).
     *
     * $exclude:        array('X','YYY') to exclude 'X' and 'YYY'
     *
     * @param  array $exclude array of literal or regex
     * @param  int $option regex option
     * @access public
     * @static
     * @see    self::charWithExclusion()
     * @api
     */
    public static function stringWithExclusion(
        array $exclude,
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */;

    /**
     * Return regex to match balanced string structure.
     *
     * Balanced structure has recursive start and end delimiter. Such as
     * '{y + {x + 1}}', where left delimiter is '{', right delimiter is '}'.
     *
     * - May allow delimiters in quoted substring (OPTION_QUOTEOK).
     * - May allow escaped delimiter (OPTION_BACKSLASH).
     *
     * regex:
     * (                        # open group
     *   L                      # left delimiter
     *    (                     # inner part
     *       (?:                # non+capture group
     *          excludeChars    # exclude L|R|\, allow '\.'
     *          |(?-2)          # or recursive same pattern
     *       )*+                # non-capture repeat
     *     )                    # inner part end
     *   R                      # right dilimiter
     * )                        # close group
     *
     * @param  string $left left delimiter, literal or regex
     * @param  string $right right delimiter, literal or regex
     * @param  int $option regex option
     * @return string
     * @access public
     * @static
     * @api
     */
    public static function balancedString(
        /*# string */ $left,
        /*# string */ $right,
        /*# int */ $option = RegExOption::OPTION_ALLOPTS
    )/*# : string */;
}
