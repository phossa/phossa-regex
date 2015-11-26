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

/**
 * Date and time related regex
 *
 * @static
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Datetime extends StaticAbstract
{
    /**
     * Return regex to match traditional or iso date
     *
     * traditional date
     *                  [m]m/[d]d/[yy]yy
     *                  [d]d/[m]m/[yy]yy
     *
     * iso date
     *                  yyyy-[m]m-[d]d
     *
     * It is not possible to acurrately validate a date with regex
     *
     * @param  void
     * @return string
     * @access public
     * @see    strtotime()
     * @static
     */
    public static function date()/*# : string */
    {
        // month with 30 days, 4/6/9/11
        $m1 = '(?:0?[469]|11)';
        // month with 31 days, 1/3/5/7/8/10/12
        $m2 = '(?:0?[13578]|1[02])';
        // month with 28 or 29 days
        $m3 = '(?:0?2)';
        // 30 days
        $d1 = '(?:[0]?[1-9]|[12][0-9]|30)';
        // 31 days
        $d2 = '(?:[0]?[1-9]|[12][0-9]|3[01])';
        // 28-29 days
        $d3 = '(?:[0]?[1-9]|[12][0-9])';

        // m/d order
        $t1 = sprintf('(?:%s/%s)|(?:%s/%s)|(?:%s/%s)',
                $m1, $d1, $m2, $d2, $m3, $d3);

        // d/m order
        $t2 = sprintf('(?:%s/%s)|(?:%s/%s)|(?:%s/%s)',
                $d1, $m1, $d2, $m2, $d3, $m3);

        // traditional
        $tra = sprintf('(?:%s|%s)/%s', $t1, $t2, '(?:[0-9]{2})?[0-9]{2}');

        // m-d order
        $i1  = sprintf('(?:%s-%s)|(?:%s-%s)|(?:%s-%s)',
                $m1, $d1, $m2, $d2, $m3, $d3);
        // iso
        $iso = sprintf('%s-%s', '[0-9]{4}', $i1);

        // Ymd 20140430
        $short = sprintf('%s(?:%s%s)|(?:%s%s)|(?:%s%s)',
                 '[0-9]{4}', $m1, $d1, $m2, $d2, $m3, $d3);

        return sprintf('(?:%s|%s|%s)', $tra, $iso, $short);
    }

    /**
     * Return regex to match time, 24/12 hour format  23:51:00
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function time()/*# : string */
    {
        return sprintf(
            // format string
            '%s%s%s',
            // hour
            '(?:1[0-2]|0?[1-9])|(?:2[0-3]|[01]?[0-9])',
            // minute
            '(?::[0-5]?[0-9])',
            // optional seconds
            '(?::[0-5]?[0-9])?'
        );
    }
}
