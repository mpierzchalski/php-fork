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
    public function beginExecute()
    {}

    public function startPid(PidResultsHandler $resultHandler)
    {}

    public function killPid(PidResultsHandler $resultHandler)
    {}

    public function endExecute(ExecResultsHandler $resultHandler)
    {}
} 