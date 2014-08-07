<?php

namespace PHPFork\Handler;

/**
 * @package   php-fork
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

class PidResultsHandler
{

    private $startPid = null;

    private $execute = null;

    private $killPid = null;

    /**
     * @var null|ExecResultsHandler
     */
    private $execHandler = null;

    /**
     * Konstuktor
     *
     * @param $_execHandler
     */
    function __construct(ExecResultsHandler $_execHandler)
    {
        $this->execHandler = $_execHandler;
    }

    /**
     * @return ExecResultsHandler|null
     */
    public function getExecHandler()
    {
        return $this->execHandler;
    }

    /**
     * @param null $startPid
     * @return $this
     */
    public function setStartPid($startPid)
    {
        $this->startPid = $startPid;
        return $this;
    }

    /**
     * @return null
     */
    public function getStartPid()
    {
        return $this->startPid;
    }

    /**
     * @param null $execute
     * @return $this
     */
    public function setExecute($execute)
    {
        $this->execute = $execute;
        return $this;
    }

    /**
     * @return null
     */
    public function getExecute()
    {
        return $this->execute;
    }

    /**
     * @param null $killPid
     * @return $this
     */
    public function setKillPid($killPid)
    {
        $this->killPid = $killPid;
        return $this;
    }

    /**
     * @return null
     */
    public function getKillPid()
    {
        return $this->killPid;
    }

}