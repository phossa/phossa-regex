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
        /*# string */ $modifiers = '',
        /*# string */ $delimiter = '/#@%~&!'
    )/*# : string */ {
        // modifier '1' is converted from (string) TRUE
        $m = $modifiers === '1' ? '' : $modifiers;

        // try different delimiter
        for ($i = 0, $l = strlen($delimiter); $i < $l; ++$i) {
            if (strpos($regex, $delimiter[$i]) === false) {
                return $delimiter[$i] . $regex . $delimiter[$i] . $m;
            }
        }

        // use '/' and escape any unescaped '/' in $regex
        return static::wrapWithChar($regex, '/') . $m;
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
        $pattern_modifier  = false,
        $named_group       = false,
        $boundary          = false,
        $anchor            = false
    )/*# : string */ {
        // add boundary
        if ($boundary) {
            $regex = static::addBoundary($regex);
        }

        // add anchor
        if ($anchor) {
            $regex = static::addBoundary($regex, '^', '$');
        }

        // add group
        if ($named_group) {
            $regex = static::groupRegEx(
                $regex,
                $named_group === true ? '' : $named_group
            );
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
    public static function wrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\'
    )/*# : string */ {
        // find all chars (with or without escape)
        $find = static::toPattern(
                    sprintf('(?:%s)*+(?:%s)',
                        preg_quote($escape),
                        preg_quote($char)
                    )
        );

        // replace
        $str = preg_replace_callback($find,
            function($matches) use ($char, $escape) {
            return $matches[0] === $char ?
                sprintf('%s%s', $escape, $char) :
                sprintf('%s%s%s', $escape, $escape, $matches[0]
            );
        }, $string);

        // wrap
        return sprintf('%s%s%s', $char, $str, $char);
    }

    /**
     * {@inheritDoc}
     */
    public static function unwrapWithChar(
        /*# string */ $string,
        /*# string */ $char = '"',
        /*# string */ $escape = '\\'
    )/*# : string */ {
        // make sure $string is wrapped with $char
        $pat = static::toPattern(RegEx::stringWithOpenClose($char, $char));
        if (!preg_match($pat, $string, $m) || $m[0] !== $string) {
            return $string;
        }

        // find all chars (with or without escape)
        $find = static::toPattern(
                    sprintf('(?:%s)*+(?:%s)',
                        preg_quote($escape),
                        preg_quote($char)
                    )
        );

        // remove escape
        return preg_replace_callback($find,
            function($matches) use ($char, $escape) {
            if ($matches[0] === $char) {
                return '';
            } elseif ($matches[0] === ($escape . $char)) {
                return $char;
            } else {
                return substr($matches[0], strlen($escape . $escape));
            }
            return $matches[0];
        }, $string);
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
        /*# string */ $end = '(?=\b|[^\d])'
    )/*# : string */ {
        return sprintf('%s%s%s', $begin, $regex, $end);
    }
}
