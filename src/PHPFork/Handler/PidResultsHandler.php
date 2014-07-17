<?php

namespace PHPFork\Handler;

/**
 * @package   SmfX
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

class PidResultsHandler
{

    private $_startPid = null;

    private $_execute = null;

    private $_killPid = null;

    /**
     * @var null|ExecResultsHandler
     */
    private $_execHandler = null;

    /**
     * Konstuktor
     *
     * @param $_execHandler
     */
    function __construct(ExecResultsHandler $_execHandler)
    {
        $this->_execHandler = $_execHandler;
    }

    /**
     * @return ExecResultsHandler|null
     */
    public function getExecHandler()
    {
        return $this->_execHandler;
    }

    /**
     * @param null $startPid
     * @return $this
     */
    public function setStartPid($startPid)
    {
        $this->_startPid = $startPid;
        return $this;
    }

    /**
     * @return null
     */
    public function getStartPid()
    {
        return $this->_startPid;
    }

    /**
     * @param null $execute
     * @return $this
     */
    public function setExecute($execute)
    {
        $this->_execute = $execute;
        return $this;
    }

    /**
     * @return null
     */
    public function getExecute()
    {
        return $this->_execute;
    }

    /**
     * @param null $killPid
     * @return $this
     */
    public function setKillPid($killPid)
    {
        $this->_killPid = $killPid;
        return $this;
    }

    /**
     * @return null
     */
    public function getKillPid()
    {
        return $this->_killPid;
    }

}