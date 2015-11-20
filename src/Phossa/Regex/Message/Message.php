<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Regex\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Regex
 *
 * @package \Phossa\Regex
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Message extends MessageAbstract
{
    /**#@+
     * @var   int
     * @type  int
     */

    /**
     * Wrong log level name
     */
    const WRONG_LOG_LEVEL       = 1511200931;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
    ];
}