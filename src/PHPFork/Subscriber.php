<?php
/**
 * @package php-fork
 * @author  Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license MIT
 */

namespace PHPFork;


use PHPFork\Handler\ExecResultsHandler;
use PHPFork\Handler\PidResultsHandler;

class Subscriber
{
    /**
     * Invokes at the beginning
     */
    public function beginExecute()
    {}

    /**
     * Invokes at the start each pid
     *
     * @param PidResultsHandler $resultHandler
     */
    public function startPid(PidResultsHandler $resultHandler)
    {}

    /**
     * Invokes at the killing pid
     *
     * @param PidResultsHandler $resultHandler
     */
    public function killPid(PidResultsHandler $resultHandler)
    {}

    /**
     * Invokes at the end
     *
     * @param ExecResultsHandler $resultHandler
     */
    public function endExecute(ExecResultsHandler $resultHandler)
    {}
} 