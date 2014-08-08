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
     * Konstuktor
     *
     * @param $execHandler
     */
    function __construct(ExecResultsHandler $execHandler)
    {
        $this->execHandler = $execHandler;
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