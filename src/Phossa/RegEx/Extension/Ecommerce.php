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
 * Ecommerce related regex
 *
 * @static
 * @package \Phossa\RegEx
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Ecommerce extends StaticAbstract
{
    /**
     * Return regex to match email address.
     *
     * RFC822: [\w!#$%&\'*+/=?`{|}~^-]+(?:\.[\w!#$%&\'*+/=?`{|}~^-]+)*
     *
     * @param  bool $rfc RFC822 compliant or not
     * @return string
     * @access public
     * @static
     */
    public static function emailAddress($rfc = false)/*# : string */
    {
        $user = $rfc ?
            '[\w!#$%&\'*+/=?`{|}~^-]+(?:\.[\w!#$%&\'*+/=?`{|}~^-]+)*' :
            '\w++(?:\.[\w-]++)*+';
        return sprintf('%s@%s', $user, static::hostName());
    }

    /**
     * Return regex to match fully qualified hostname
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function hostName()/*# : string */
    {
        $subdomain = '[a-z0-9]|[a-z0-9][-a-z0-9]*[a-z0-9]';
        $topdomain = '[a-zA-Z]{2,6}';
        return sprintf('(?i:%s\.)+%s', $subdomain, $topdomain);
    }

    /**
     * Return regex to match credit card number
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function creditCardNumber()/*# : string */
    {
        $regex = sprintf(
            // format string
            '(?:%s|%s|%s|%s|%s|%s)',
            // visa
            '(?<visa>4[0-9]{12}(?:[0-9]{3})?)',
            // mastercard
            '(?<mastercard>5[1-5][0-9]{14})',
            // discover
            '(?<discover>6(?:011|5[0-9][0-9])[0-9]{12})',
            // amex
            '(?<amex>3[47][0-9]{13})',
            // dinners
            '(?<diners>3(?:0[0-5]|[68][0-9])[0-9]{11})',
            // jcb
            '(?<jcb>(?:2131|1800|35\d{3})\d{11})'
        );
        return $regex;
    }

    /**
     * Return regex to match US social security number
     *
     * format: AAA-GG-SSSS
     *  AAA: area number, can't be 000 or 666, and <= 772
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function usSocialSecurityNumber()/*# : string */
    {
        return sprintf(
            // format string
            '%s%s%s',
            // area number
            '(?!000|666)(?:[0-6][0-9]{2}|7(?:[0-6][0-9]|7[0-2]))',
            // group number
            '-(?!00)[0-9]{2}',
            // serial number
            '-(?!0000)[0-9]{4}'
        );
    }

    /**
     * Return regex to match US phone number (NANP)
     *
     * allowed format:  +1 223 456 7890
     *                  223-456-7890
     *                  223.456.7890
     *                  223 456 7890
     *                  (223) 4567890
     *                  (223) 456 7890
     *                  (223) 456.7890
     *                  (223) 456-7890 standard
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function usPhoneNumber()/*# : string */
    {
        return sprintf(
            // format string
            '%s%s%s%s%s%s',
            // optional us country code
            '(?:(?:\+?+1[ .-]?+)?',
            // area code with optional '()'
            '(\()?(?:[2-9][0-8][0-9])(?(-1)\) )',
            // first optional seperator
            '([ .-])?)?',
            // second set numbers
            '(?:[2-9][0-9]{2})',
            // second optional seperator
            '(?(-1)\g{-1}|[ .-]?+)',
            // last set of numbers
            '(?:[0-9]{4})'
        );
    }

    /**
     * Return regex to match US zipcode
     *
     * @param  void
     * @return string
     * @access public
     * @static
     */
    public static function usZipCode()/*# : string */
    {
        return '[0-9]{5}(?:-[0-9]{4})?';
    }
}
