<?php

namespace PHPFork;

declare(ticks = 1);

use PHPFork\Exception\SubscriberException;
use PHPFork\Handler\ExecResultsHandler;
use PHPFork\Handler\PidResultsHandler;

/**
 * @package   php-fork
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */
class Fork
{
    const LISTENER_BEGINEXECUTE = 'beginExecute';
    const LISTENER_STARTPID     = 'startPid';
    const LISTENER_KILLPID      = 'killPid';
    const LISTENER_ENDEXECUTE   = 'endExecute';

    /**
     * @var Fork
     */
    public static $instance = null;

    /**
     * @var float
     */
    private $requestTime;

    /**
     * @var array
     */
    private $forkedPids = [];

    /**
     * @var array
     */
    private $subscribers = [];

    /**
     * @var null|ExecResultsHandler
     */
    private $execResults;

    /**
     * @var null|PidResultsHandler
     */
    private $pidResults;

    /**
     * @var int
     */
    protected $timeLimit = 60;

    /**
     * @var int
     */
    protected $maxParallelProcesses = 2;

    /**
     * Construct
     *
     * @param int $maxParallelProcesses
     * @param int $timeLimit - w s
     */
    public function __construct($maxParallelProcesses = null, $timeLimit = null)
    {
        if (null !== $maxParallelProcesses) {
            $this->maxParallelProcesses = $maxParallelProcesses;
        }
        if (null !== $timeLimit) {
            $this->timeLimit = $timeLimit;
        }

        $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'];
        $this->setExecResults(new ExecResultsHandler());
    }

    /**
     * @param null|\PHPFork\Handler\ExecResultsHandler $execResults
     */
    public function setExecResults($execResults)
    {
        $this->execResults = $execResults;
    }

    /**
     * @return null|\PHPFork\Handler\ExecResultsHandler
     */
    public function getExecResults()
    {
        return $this->execResults;
    }

    /**
     * @param int $maxParallelProcesses
     */
    public function setMaxParallelProcesses($maxParallelProcesses)
    {
        $this->maxParallelProcesses = $maxParallelProcesses;
    }

    /**
     * @return int
     */
    public function getMaxParallelProcesses()
    {
        return $this->maxParallelProcesses;
    }

    /**
     * @param null|\PHPFork\Handler\PidResultsHandler $pidResults
     */
    public function setPidResults($pidResults)
    {
        $this->pidResults = $pidResults;
    }

    /**
     * @return null|\PHPFork\Handler\PidResultsHandler
     */
    public function getPidResults()
    {
        return $this->pidResults;
    }

    /**
     * @param int $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }


    /**
     * Executes
     *
     * @param callable $e
     * @return void
     */
    public function execute(\Closure $e)
    {
        // launching BEGINEXECUTE listener
        $this->launchSubscribers(self::LISTENER_BEGINEXECUTE);
        do {
            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->forkedPids[] = $pid;
            } else if ($pid == 0) {
                $this->pidResults = new PidResultsHandler($this->getExecResults());

                // launching STARTPID listener
                $this->launchSubscribers(self::LISTENER_STARTPID);

                // launching execute() and saves the result
                $this->pidResults->setExecute(call_user_func($e, $this->pidResults));

                pcntl_signal(SIGTERM, function($signal) {
                    // launching KILLPID listener
                    $this->launchSubscribers(self::LISTENER_KILLPID);
                });

                posix_kill(posix_getpid(), SIGTERM);
                exit;
            }

            // Checks if maximum amount of forked processes has been exceeded
            while (count($this->forkedPids) >= $this->getMaxParallelProcesses()) {
                $this->unsetForkedPid(pcntl_waitpid(-1, $status, WNOHANG));
                usleep(100);
            }

        } while (((microtime(true) - $this->requestTime) <= $this->getTimeLimit()));

        // Checks if all forked processes are ended
        while (count($this->forkedPids)) {
            $this->unsetForkedPid(pcntl_waitpid(-1, $status, WNOHANG));
            usleep(100);
        }

        // launching ENDEXECUTE listener
        $this->launchSubscribers(self::LISTENER_ENDEXECUTE);
    }

    /**
     * Executes collection
     *
     * @param $collection
     * @return \Generator
     */
    public function executeCollection($collection)
    {
        // launching BEGINEXECUTE listener
        $this->launchSubscribers(self::LISTENER_BEGINEXECUTE);
        foreach ($collection as $k => $e) {

            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->forkedPids[] = $pid;
            } else if ($pid == 0) {
                $this->pidResults = new PidResultsHandler(posix_getppid(), posix_getpid(), $this->getExecResults(), $k);

                // launching STARTPID listener
                $this->launchSubscribers(self::LISTENER_STARTPID);

                // launching execute() and saves the result
                $this->pidResults->setExecute(call_user_func($e, $this->pidResults));

                pcntl_signal(SIGTERM, function($signal) {
                    // launching KILLPID listener
                    $this->launchSubscribers(self::LISTENER_KILLPID);
                });

                posix_kill(posix_getpid(), SIGTERM);
                exit;
            }

            // Checks if maximum amount of forked processes has been exceeded
            while (count($this->forkedPids) >= $this->getMaxParallelProcesses()) {
                $this->unsetForkedPid(pcntl_waitpid(-1, $status, WNOHANG));
                usleep(100);
            }

            if ((microtime(true) - $this->requestTime) >= $this->getTimeLimit()) {
                break;
            }
        }

        // Checks if all forked processes are ended
        while (count($this->forkedPids)) {
            $this->unsetForkedPid(pcntl_waitpid(-1, $status, WNOHANG));
            usleep(100);
        }

        // launching ENDEXECUTE listener
        $this->launchSubscribers(self::LISTENER_ENDEXECUTE);
    }

    /**
     * Unset pid
     *
     * @param int $pid
     */
    private function unsetForkedPid($pid)
    {
        if (($foundPidKey = array_search($pid, $this->forkedPids)) !== false) {
            unset($this->forkedPids[$foundPidKey]);
        }
    }

    /**
     * Registers subscriber
     *
     * @param Subscriber $subscriber
     * @return $this
     * @throws SubscriberException
     */
    public function registerSubscriber($subscriber)
    {
        if (in_array($subscriber, $this->subscribers, true)) {
            throw new SubscriberException(sprintf("Subscriber %s is already registered!", get_class($subscriber)));
        }
        $this->subscribers[] = $subscriber;
        return $this;
    }

    /**
     * Launches subscribers
     *
     * @param string $type - self::LISTENER_*
     */
    private function launchSubscribers($type)
    {
        $resultHandler = (in_array($type, array(self::LISTENER_STARTPID, self::LISTENER_KILLPID)))
            ? $this->getPidResults()
            : $this->getExecResults();

        foreach ($this->subscribers as &$subscriber) {
            $result = call_user_func(array($subscriber, $type), $resultHandler);
            $resultHandler->{'set' . ucfirst($type)}($subscriber, $result);
        }
    }
}