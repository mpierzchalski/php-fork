<?php

namespace PHPFork;

declare(ticks = 1);

use PHPFork\Handler\ExecResultsHandler;
use PHPFork\Handler\PidResultsHandler;

/**
 * @package   SmfX
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */
class Fork
{
    const DEBUG_ITEM = 1;
    const DEBUG_SUMMARY = 2;

    const LISTENER_BEGINEXECUTE = 'beginExecute';
    const LISTENER_BEGINPROC    = 'beginProcess';
    const LISTENER_STARTPID     = 'startPid';
    const LISTENER_KILLPID      = 'killPid';
    const LISTENER_ENDLOOP      = 'endLoop';
    const LISTENER_ENDEXECUTE   = 'endExecute';

    /**
     * @var Fork
     */
    static public $instance = null;

    /**
     * @var float
     */
    private $_requestTime = null;

    /**
     * @var array
     */
    private $_childPids = array();

    /**
     * @var array
     */
    private $_listeners = array();

    /**
     * @var null|ExecResultsHandler
     */
    private $_execResults = null;

    /**
     * @var null|PidResultsHandler
     */
    private $_pidResults = null;

    /**
     * @var int
     */
    protected $_timeLimit = 60;

    /**
     * @var int
     */
    protected $_loopExecutionTime = 1000;

    /**
     * @var int
     */
    protected $_maxProcesses = 2;

    /**
     * @var null
     */
    protected $_debugMode = null;

    /**
     * Construct
     *
     * @param int $maxProcesses
     * @param int $loopExecutionTime - w ms
     * @param int $timeLimit - w s
     * @param int $debugMode
     */
    public function __construct($maxProcesses = null, $loopExecutionTime = null, $timeLimit = null, $debugMode = null)
    {
        $this->_requestTime  = $_SERVER['REQUEST_TIME_FLOAT'];
        if (null !== $maxProcesses) $this->_maxProcesses = $maxProcesses;
        if (null !== $loopExecutionTime) $this->_loopExecutionTime = $loopExecutionTime;
        if (null !== $timeLimit) $this->_timeLimit = $timeLimit;
        if (null !== $debugMode) $this->_debugMode = $debugMode;

        $this->_execResults = new ExecResultsHandler();
    }

    /**
     * @return null
     */
    public function getDebugMode()
    {
        return $this->_debugMode;
    }

    /**
     * @param null $debugMode
     * @return $this
     */
    public function setDebugMode($debugMode)
    {
        $this->_debugMode = $debugMode;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxProcesses()
    {
        return $this->_maxProcesses;
    }

    /**
     * @param int $maxProcesses
     * @return $this
     */
    public function setMaxProcesses($maxProcesses)
    {
        $this->_maxProcesses = $maxProcesses;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeLimit()
    {
        return $this->_timeLimit;
    }

    /**
     * @param int $timeLimit
     * @return $this
     */
    public function setTimeLimit($timeLimit)
    {
        $this->_timeLimit = $timeLimit;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoopExecutionTime()
    {
        return $this->_loopExecutionTime;
    }

    /**
     * @param int $loopExecutionTime
     * @return $this
     */
    public function setLoopExecutionTime($loopExecutionTime)
    {
        $this->_loopExecutionTime = $loopExecutionTime;
        return $this;
    }

    /**
     * @param ExecResultsHandler|null $execResults
     * @return $this
     */
    public function setExecResults($execResults)
    {
        $this->_execResults = $execResults;
        return $this;
    }

    /**
     * @return ExecResultsHandler|null
     */
    public function getExecResults()
    {
        return $this->_execResults;
    }

    /**
     * @param PidResultsHandler|null $pidResults
     * @return $this
     */
    public function setPidResults($pidResults)
    {
        $this->_pidResults = $pidResults;
        return $this;
    }

    /**
     * @return PidResultsHandler|null
     */
    public function getPidResults()
    {
        return $this->_pidResults;
    }

    /**
     * Executes
     *
     * @param callable $e
     * @return void
     */
    public function execute(\Closure $e)
    {
        $_instance = $this;
        $this->_launchListener(self::LISTENER_BEGINEXECUTE);
        do {
            $this->_launchListener(self::LISTENER_BEGINLOOP);
            $loopStartTime = microtime(true);
            $pid           = pcntl_fork();
            if ($pid > 0) {
                $this->_childPids[] = $pid;
            } else if ($pid == 0) {
                $this->_pidResults = new PidResultsHandler($this->getExecResults());

                $this->_launchListener(self::LISTENER_STARTPID);
//                if ($_instance->getDebugMode() == self::DEBUG_ITEM) {
//                    print sprintf('Starting pid %d' . PHP_EOL, posix_getpid());
//                }

                // launching execute() and saves the result
                $this->_pidResults->setExecute(call_user_func($e, $this->_pidResults));

                pcntl_signal(SIGTERM, function($signal) use ($_instance, $loopStartTime) {
                    $this->_launchListener(self::LISTENER_KILLPID);
//                    if ($_instance->getDebugMode() == self::DEBUG_ITEM) {
//                        print sprintf('End of pid %d in time: %s ms; SIG => %d' . PHP_EOL,
//                            posix_getpid(),
//                            ((microtime(true) - $loopStartTime) * 1000),
//                            $signal
//                        );
//                    }
                });

                posix_kill(posix_getpid(), SIGTERM);
                exit;
            }

            while (count($this->_childPids) >= $this->getMaxProcesses()) {
                $thisPid = pcntl_waitpid(-1, $status, WNOHANG);
                foreach ($this->_childPids as $key => $childPid) {
                    if($thisPid == $childPid) unset($this->_childPids[$key]);
                }
                usleep(100);
            }
            $loopExecutionTimeMs = (microtime(true) - $loopStartTime) * 1000;
            if ($loopExecutionTimeMs < $this->getLoopExecutionTime()) {
                $sleepingTimeMs = $this->getLoopExecutionTime() - $loopExecutionTimeMs;
//                if ($this->getDebugMode() == self::DEBUG_ITEM) {
//                    print sprintf('Sleeping pid %d for: %s ms' . PHP_EOL, posix_getpid(), $sleepingTimeMs);
//                }
                usleep($sleepingTimeMs * 1000);
            }
            $this->_launchListener(self::LISTENER_ENDLOOP);
        } while (((microtime(true) - $this->_requestTime) <= ($this->getTimeLimit()-2)));

        $this->_launchListener(self::LISTENER_ENDEXECUTE);
//        if ($this->getDebugMode() == self::DEBUG_SUMMARY) {
//            print sprintf('End of file %s in time: %s ms' . PHP_EOL,
//                $_SERVER['SCRIPT_NAME'],
//                ((microtime(true) - $this->_requestTime) * 1000)
//            );
//        }
    }

    /**
     * Adds listener
     *
     * @param string $type - self::LISTENER_*
     * @param \Closure $e
     * @return $this
     * @throws \Exception
     */
    public function addListener($type, \Closure $e)
    {
        if (array_key_exists($type, $this->_listeners)) {
            throw new \Exception('This listener is already added : ' . $type . '!');
        }
        $this->_listeners[$type] = $e;
        return $this;
    }

    /**
     * Launches listeners
     *
     * @param string $type - self::LISTENER_*
     */
    private function _launchListener($type)
    {
        if (!array_key_exists($type, $this->_listeners)) {
            return;
        }
        $resultHandler = (in_array($type, array(self::LISTENER_STARTPID, self::LISTENER_KILLPID)))
            ? $this->getPidResults() : $this->getExecResults();

        $result = call_user_func($this->_listeners[$type], $resultHandler);
        $resultHandler->{'set' . ucfirst($type)}($result);
    }
}