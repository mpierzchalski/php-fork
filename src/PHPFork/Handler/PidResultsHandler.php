<?php

namespace PHPFork\Handler;
use PHPFork\Subscriber;

/**
 * @package   php-fork
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

class PidResultsHandler
{
    /**
     * @var int
     */
    private $parentPid;

    /**
     * @var int
     */
    private $pid;

    /**
     * @var string|null
     */
    private $pidCustomKey;

    /**
     * @var array
     */
    private $startPid = [];

    /**
     * @var mixed
     */
    private $execute;

    /**
     * @var array
     */
    private $killPid = [];

    /**
     * @var ExecResultsHandler
     */
    private $execHandler;

    /**
     * Construct
     *
     * @param int                $parentPid
     * @param int                $pid
     * @param ExecResultsHandler $execHandler
     * @param string             $pidCustomKey
     */
    function __construct($parentPid, $pid, ExecResultsHandler $execHandler, $pidCustomKey = null)
    {
        $this->parentPid    = $parentPid;
        $this->pid          = $pid;
        $this->pidCustomKey = $pidCustomKey;
        $this->execHandler  = $execHandler;
    }

    /**
     * @return int
     */
    public function getParentPid()
    {
        return $this->parentPid;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return null|string
     */
    public function getPidCustomKey()
    {
        return $this->pidCustomKey;
    }

    /**
     * @return ExecResultsHandler|null
     */
    public function getExecHandler()
    {
        return $this->execHandler;
    }

    /**
     * @param Subscriber    $subscriber
     * @param int           $startPid
     * @return $this
     */
    public function setStartPid(Subscriber $subscriber, $startPid)
    {
        $this->startPid[get_class($subscriber)] = $startPid;
        return $this;
    }

    /**
     * @param Subscriber $subscriber
     * @return null
     */
    public function getStartPid(Subscriber $subscriber)
    {
        $class = get_class($subscriber);
        if (isset($this->startPid[$class])) {
            return $this->startPid[$class];
        }
        return null;
    }

    /**
     * @param mixed $execute
     * @return $this
     */
    public function setExecute($execute)
    {
        $this->execute = $execute;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExecute()
    {
        return $this->execute;
    }

    /**
     * @param Subscriber    $subscriber
     * @param mixed         $killPid
     * @return $this
     */
    public function setKillPid(Subscriber $subscriber, $killPid)
    {
        $this->killPid[get_class($subscriber)] = $killPid;
        return $this;
    }

    /**
     * @param Subscriber $subscriber
     * @return null
     */
    public function getKillPid(Subscriber $subscriber)
    {
        $class = get_class($subscriber);
        if (isset($this->killPid[$class])) {
            return $this->killPid[$class];
        }
        return null;
    }

}