<?php
/**
 * @package php-fork
 * @author  Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license MIT
 */

namespace PHPFork\Subscriber;


use PHPFork\Handler\ExecResultsHandler;
use PHPFork\Handler\PidResultsHandler;

class DebugSubscriber
{
    public function beginExecute()
    {
        return 1;
    }

    public function startPid(PidResultsHandler $resultHandler)
    {
        return $resultHandler->getExecHandler()->getBeginExecute($this)*10;
    }

    public function killPid(PidResultsHandler $resultHandler)
    {
        print 'KillPid: ' . $resultHandler->getStartPid()*10;
    }

    public function endExecute(ExecResultsHandler $resultHandler)
    {
        print 'Koniec: ' . $resultHandler->getBeginExecute($this);
    }

} 