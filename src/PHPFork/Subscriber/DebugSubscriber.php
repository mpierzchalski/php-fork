<?php
/**
 * @package php-fork
 * @author  Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license MIT
 */

namespace PHPFork\Subscriber;


use PHPFork\Handler\ExecResultsHandler;
use PHPFork\Handler\PidResultsHandler;
use PHPFork\Subscriber;

class DebugSubscriber extends Subscriber
{
    /**
     * {@inheritdoc}
     */
    public function beginExecute()
    {
        print 'beginExecute at: ' . date('c');
    }

    /**
     * {@inheritdoc}
     */
    public function startPid(PidResultsHandler $resultHandler)
    {
        return microtime(true);
    }

    /**
     * {@inheritdoc}
     */
    public function killPid(PidResultsHandler $resultHandler)
    {
        $executionPidTime = round((microtime(true)-$resultHandler->getStartPid($this))*1000);
        print sprintf('killPid in time: %s ms', $executionPidTime) . PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function endExecute(ExecResultsHandler $resultHandler)
    {
        print 'End.';
    }

} 