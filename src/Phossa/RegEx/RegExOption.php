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
 * Regular expression related options
 *
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class RegExOption
{
    /**#@+
     *
     * @const
     * @access public
     */

    /**
     * dot in regex matches newline also. Useful for allowing quoted string
     * span over multiple lines.
     */
    const OPTION_MULTILINE      = 1;

    /**
     * Allow escape characters with BACKSLASH in result. Useful for allowing
     * or disallowing escaped quotes in quoted string.
     */
    const OPTION_BACKSLASH      = 2;

    /**
     * Default to match closed structure only. For example, matching quoted
     * strings. If UNSET, can be used to find out non-closed matches. e.g.
     * can find non-closed quotes etc.
     */
    const OPTION_CLOSURE        = 4;

    /**
     * Parameters are literals, not regex. If UNSET, means the parameters
     * are regex, should not be preg_quoted() in the method again.
     */
    const OPTION_LITERAL        = 8;

    /**
     * Combination of the previous options
     */
    const OPTION_DEFAULT        = 1023;

    /**
     * Combination of the previous without OPTION_LITERAL
     */
    const OPTION_DEFAULT_REG    = 1015;

    /**
     * Allow quoted substrings in matched result. Treat quoted substrings as
     * black box.
     */
    const OPTION_QUOTEOK        = 1024;

    /**
     * Allow heredoc/nowdoc substrings in matched result. Any excluded char
     * or string may appear in the heredoc/nowdoc substrings.
     */
    const OPTION_HEREDOC        = 2048;

    /**
     * Combination of all the options.
     */
    const OPTION_ALLOPTS        = 65535;

    /**
     * Combination of all the options without OPTION_LITERAL
     */
    const OPTION_ALLOPTS_REG    = 65527;

    /**#@-*/
}
