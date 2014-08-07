<?php
/**
 * @package php-fork
 * @author  Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license MIT
 */

namespace PHPFork\Exception;


use Exception;

class SubscriberException extends \Exception
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf("SubscriberException has been thrown with message: %s", $message), $code, $previous);
    }

} 