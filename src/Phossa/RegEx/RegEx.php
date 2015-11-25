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
 * Implemetation of RegExInterface
 *
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @see     Phossa\RegEx\RegExInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class RegEx extends StaticAbstract implements RegExInterface
{
    /**
     * {@inheritDoc}
     */
    public static function charWithExclusion(
        array $exclude = [],
        array $allowed = [],
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        // dot-for-all mode
        $s = $option & RegExOption::OPTION_MULTILINE ? 's' : '';

        // allow escape char '\'
        if ($option & RegExOption::OPTION_BACKSLASH) {
            // add backslash char
            $exclude = array_unique(array_merge($exclude, array('\\')));
            // add escape sequense '\.' regex
            $allowed = array_unique(array_merge($allowed, array('\\\\.')));
        }

        // convert exclude string 'YYY' to regex (?!YYY|(?<=Y)YY|(?<=YY)Y)
        $e = '';
        foreach ($exclude AS $x) {
            if ($option & RegExOption::OPTION_LITERAL) {
                $e .= preg_quote($x) . '|';
                if (strlen($x) > 1) {
                    for ($i = 1, $l = strlen($x); $i < $l; ++$i) {
                        $e .= '(?<=' . preg_quote(substr($x, 0, $i)) . ')' .
                            preg_quote(substr($x, $i)) . '|';
                    }
                }
            } else {
                $e .= ($x === '\\' ? '\\\\' : $x). '|';
            }
        }

        if ($e) {
            // remove the ending '|'
            $e = sprintf('(?!%s).', substr($e, 0, -1));
        } else {
            $e = '.';
        }

        // allowed regex
        $a = $allowed ? '|' . join('|', $allowed) : '';

        return sprintf('(?%s:%s%s)', $s, $e, $a);
    }

    /**
     * {@inheritDoc}
     */
    public static function singleQuotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT,
        /*# string */ $quoteCharacter = "'"
    )/*# : string */ {
        // start part
        $start = static::quoteLiteral($quoteCharacter, $option);

        // end part
        $end  = static::closeWith($quoteCharacter, $option);

        // middle part
        $middle = static::charWithExclusion(
            [ $quoteCharacter ], [], $option
        );

        // the result regex
        return sprintf('%s%s*+%s', $start, $middle, $end);
    }

    /**
     * {@inheritDoc}
     */
    public static function doubleQuotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        return static::singleQuotedString(
            $option | RegExOption::OPTION_LITERAL, '"'
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function quotedString(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        return sprintf('%s|%s',
            static::singleQuotedString($option | RegExOption::OPTION_LITERAL),
            static::doubleQuotedString($option)
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function unEscapedChar(
        /*# string */ $char,
        /*# int */ $option = RegExOption::OPTION_DEFAULT,
        /*# string */ $escape = '\\'
    )/*# : string */ {
        // php turns \\\\ to \\, regex sees \\ as \
        if ($escape === '\\') $escape = '\\\\';

        // escape the literal char
        if ($option & RegExOption::OPTION_LITERAL) $char = preg_quote($char);

        // the result regex
        return sprintf(
            '(?:^|(?<!%s))(?:%s|(?=(?:%s%s)++%s)%s++\K%s)',
            $escape, $char, $escape, $escape, $char, $escape, $char
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function escapedChar(
        /*# string */ $char,
        /*# int */ $option = RegExOption::OPTION_DEFAULT,
        /*# string */ $escape = '\\'
    )/*# : string */ {
        // php turns \\ to \, regex sees \\ as \
        if ($escape === '\\') $escape = '\\\\';

        // escape the literal char
        if ($option & RegExOption::OPTION_LITERAL) $char = preg_quote($char);

         // the result regex
        return sprintf(
            '(?:^|(?<!%s))(?:%s%s|(?=(?:%s%s)++%s%s)(?:%s%s)++\K%s%s)',
            $escape, $escape, $char, $escape, $escape, $escape, $char,
            $escape, $escape, $escape, $char
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function stringWithOpenClose(
        /*# string */ $open,
        /*# string */ $close,
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        // start part
        $start = static::quoteLiteral($open, $option);

        // end part
        $end   = static::closeWith($close, $option);

        if ($option & RegExOption::OPTION_QUOTEOK) {
            // allow quoted substring
            $middle = static::stringWithExclusion([ $close ], $option);
            return sprintf('%s(%s)%s', $start, $middle, $end);
        } else {
            // quotes has no effect on the close, such as C comment
            // but honor backslash
            return sprintf('%s%s*+%s',
                $start,
                static::charWithExclusion([ $close ], [], $option),
                $end
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function hereDoc(
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        return sprintf('(?sm:<<<([\'])?(\w++)(?(-2)\').+?%s)',
            $option & RegExOption::OPTION_CLOSURE ?
                '^\g{-1}\s*+;?\s*+$' :
                '(?:^\g{-1}\s*+;?\s*+$|\Z)'
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function stringWithExclusion(
        array $exclude,
        /*# int */ $option = RegExOption::OPTION_DEFAULT
    )/*# : string */ {
        if ($option & RegExOption::OPTION_QUOTEOK) {
            $excl = array_unique(
                array_merge(
                    $exclude,
                    $option & RegExOption::OPTION_LITERAL ?
                        ["'", '"'] : [ "\'", '\"']
                )
            );
            $char = static::charWithExclusion($excl, [], $option);

            // always closed for substring
            $option |= RegExOption::OPTION_CLOSURE;

            // allow quoted substring
            $quoted = static::quotedString($option);

            // allow nowdoc substring
            $here = $option & RegExOption::OPTION_HEREDOC ?
                sprintf('|%s', static::hereDoc($option)) : '';

            // the unrolling version
            $regex = sprintf('%s++(?:(?>%s%s)*%s*+)*|(?:(?>%s%s)+%s*+)',
                $char, $quoted, $here, $char, $quoted, $here, $char);
        } else {
            $char  = static::charWithExclusion($exclude, [], $option);
            $regex = sprintf('%s++', $char);
        }
        return $regex;
    }

    /**
     * {@inheritDoc}
     */
    public static function balancedString(
        /*# string */ $left,
        /*# string */ $right,
        /*# int */ $option = RegExOption::OPTION_ALLOPTS
    )/*# : string */ {
        // exclude left/right delimiters
        $e = static::charWithExclusion([$left, $right], [], $option);

        // allow quoted substring (must closed)
        $q = $option & RegExOption::OPTION_QUOTEOK ?
            static::quotedString($option | RegExOption::OPTION_CLOSURE).'|' : '';

        // allow heredoc/nowdoc substring (must closed)
        $h = $option & RegExOption::OPTION_HEREDOC ?
            static::hereDoc($option | RegExOption::OPTION_CLOSURE) . '|' : '';

        // escape the left/right delimiter
        $l = static::quoteLiteral($left,  $option);
        $r = static::quoteLiteral($right, $option);

        if ($h) {
            // heredoc allowed
            $regex = sprintf('(%s(?>%s%s%s|(?-3))*%s)', $l, $q, $h, $e, $r);
        } else {
            // heredoc not allowed
            $regex = sprintf('(%s(?>%s%s|(?-1))*%s)', $l, $q, $e, $r);
        }

        return $regex;
    }

    /**
     * Regex to close the match OR match to the end of the subject
     *
     * @param  string $close the close string or regex
     * @param  int $option regex option
     * @return string
     * @access protected
     * @static
     */
    protected static function closeWith(
        /*# string */ $close,
        /*# int */ $option
    )/*# : string */ {
        // convert literal to regex first
        $end = static::quoteLiteral($close, $option);

        // non-closure case
        if (!($option & RegExOption::OPTION_CLOSURE)) {
            if ($option & RegExOption::OPTION_MULTILINE) {
                // cross line allowed
                $end = sprintf('(?:%s|\Z)', $end);
            } else {
                // match single line only
                $end = sprintf('(?m:%s|$)', $end);
            }
        }

        return $end;
    }

    /**
     * Quote the literal if option has OPTION_LITERAL set
     *
     * @param  string|array $string literal or regex
     * @param  int $option regex option
     * @return string
     * @access protected
     * @static
     */
    protected static function quoteLiteral(
        $string,
        /*# int */ $option
    )/*# : string */ {
        if (is_array($string)) {
            return array_map(function($s) use ($option){
                return $option & RegExOption::OPTION_LITERAL ?
                    preg_quote($s) : $s;
            }, $string);
        } else {
            return $option & RegExOption::OPTION_LITERAL ?
                preg_quote($string) :
                $string;
        }
    }
}
