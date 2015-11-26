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

use Phossa\Shared\Pattern\StaticAbstract;

/**
 * Implemetation of UtilityInterface
 *
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @see     Phossa\RegEx\UtilityInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Utility extends StaticAbstract implements UtilityInterface
{
    /**
     * {@inheritDoc}
     */
    public static function toPattern(
        /*# string */ $regex,
        /*# string */ $modifiers  = '',
        /*# string */ $delimiters = '/#@%~&!'
    )/*# : string */ {
        // modifier '1' is converted from (string) TRUE
        $m = $modifiers === '1' ? '' : $modifiers;

        // try different delimiter
        for ($i = 0, $l = strlen($delimiters); $i < $l; ++$i) {
            if (strpos($regex, $delimiters[$i]) === false) {
                return $delimiters[$i] . $regex . $delimiters[$i] . $m;
            }
        }

        // add backslash to unescaped '/'
        return '/' . static::escapeUnEscaped($regex, '/') . '/' . $m;
    }

    /**
     * {@inheritDoc}
     */
    public static function validateRegEx(
        /*# string */ $regex
    )/*# : string */ {
        global $php_errormsg;

        // remember old tracking state
        $track = ini_get('track_errors');
        if ($track) {
            $message = isset($php_errormsg) ? $php_errormsg : false;
        } else {
            // turn on error tracking
            ini_set('track_errors', 1);
        }

        // test the pattern
        unset($php_errormsg);
        @preg_match(static::toPattern($regex), 'x');
        $returnvalue = isset($php_errormsg) ? $php_errormsg : '';

        // restore the tracking state
        if ($track) {
            $php_errormsg = isset($message) ? $message : false;
        } else {
            ini_set('track_errors', 0);
        }

        return $returnvalue;
    }

    /**
     * {@inheritDoc}
     */
    public static function modifyRegEx(
        /*# string */ $regex,
        $pattern_modifier  = true,
        $named_group       = false,
        $boundary          = false,
        $anchor            = false
    )/*# : string */ {
        // add group
        if ($named_group) {
            $regex = static::groupRegEx(
                $regex,
                $named_group === true ? '' : $named_group
            );
        }

        // add boundary
        if ($boundary) {
            $regex = static::addBoundary($regex);
        }

        // add anchor
        if ($anchor) {
            $regex = static::addBoundary($regex, '^', '$');
        }

        // return PCRE pattern with modifier
        if ($pattern_modifier !== false) {
            $regex = static::toPattern(
                $regex,
                (string) $pattern_modifier
            );
        }

        return $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function escapeUnEscaped(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# string */ {
        // regex for unescaped char
        $regex = RegEx::unEscapedChar($char, $option, $escape);

        // convert to pattern
        $pattern = strpos($char, '#') === false ?
            ('#' . $regex . '#') :
            ('(' . $regex . ')');

        if (preg_match_all($pattern, $string, $m, PREG_OFFSET_CAPTURE)) {
            // replacement
            $replace = $escape . $char;

            // orignal char length
            $ol = strlen($char);

            // replacement length
            $rl = strlen($replace);

            // position shift after each replacement
            $shift = 0;

            // replace matched
            foreach($m[0] as $matched) {
                $pos = $matched[1] + $shift;
                $string = substr_replace($string, $replace, $pos, $ol);
                $shift += $rl - $ol;
            }
        }

        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public function unEscapeEscaped(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# string */ {
        // regex for escaped char
        $regex = RegEx::escapedChar($char, $option, $escape);

        // convert to pattern
        $pattern = strpos($char, '#') === false ?
            ('#' . $regex . '#') :
            ('(' . $regex . ')');

        if (preg_match_all($pattern, $string, $m, PREG_OFFSET_CAPTURE)) {
            // orignal escaped char length
            $ol = strlen($escape . $char);

            // replacement lenght
            $rl = strlen($char);

            // position shift after each replacement
            $shift = 0;

            // replace matched
            foreach($m[0] as $matched) {
                $pos = $matched[1] + $shift;
                $string = substr_replace($string, $char, $pos, $ol);
                $shift += $rl - $ol;
            }
        }
        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public static function isWrappedWithChar(
        /*# string */ $string,
        /*# string */ $char,
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : bool */ {
        $len = strlen($char);

        // check start/end chars
        if (substr($string, 0, $len) === $char &&
            substr($string, -$len) === $char) {
            // find unescaped char if any
            $pat = static::toPattern(
                RegEx::unEscapedChar($char, $option, $escape)
            );
            if (preg_match_all($pat, substr($string, $len, -$len), $m)) {
                foreach($m[1] as $n) {
                    // found zero escaped $char
                    if ($n === $char) return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public static function wrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        // wrapped already
        if (static::isWrappedWithChar($string, $char, $escape, $option)) {
            return $string;
        }

        // wrap it
        return sprintf('%s%s%s',
            $char,
            static::escapeUnEscaped($string, $char, $escape, $option),
            $char
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function unWrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\',
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        if (static::isWrappedWithChar($string, $char, $escape, $option)) {
            $len = strlen($char);
            $str = substr($string, $len, -$len);
            return static::unEscapeEscaped($str, $char, $escape, $option);
        }
        // no wrapped
        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public static function groupRegEx(
        /*# string */ $regex,
        /*# string */ $name = ''
    )/*# : string */ {
        // grouped ?
        if ($regex[0] === '(') {
            $pat = static::toPattern(RegEx::balancedString('(', ')'));
            if (preg_match($pat, $regex, $m) && $m[0] === $regex &&
                $regex[1] !== '?') {
                // grouped already, just replace with name if any
                return $name ? "(?<$name>" . substr($regex, 1) : $regex;
            }
        }
        return sprintf('(%s%s)', $name ? "?<$name>" : '', $regex);
    }

    /**
     * {@inheritDoc}
     */
    public static function addBoundary(
        /*# string */ $regex,
        /*# string */ $begin = '(?<=\b|[^\d])',
        /*# string */ $end   = '(?=\b|[^\d])'
    )/*# : string */ {
        return sprintf('%s%s%s', $begin, $regex, $end);
    }

    public static function getLineColumn(
        /*# string */ &$string,
        /*# int */ $offset,
        /*# bool */ $startFromOne = true
    )/*# : array */ {
        $str  = substr($string, 0, $offset);
        $line = substr_count($str, "\n");
        $last = strrpos($str, "\n");
        $col  = strlen($str) - ($last === false ? 0 : $last + 1);
        return array(
            $line + (int) $startFromOne, $col + (int) $startFromOne
        );
    }
}
