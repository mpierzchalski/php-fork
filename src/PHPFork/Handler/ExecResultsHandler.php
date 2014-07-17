<?php

namespace PHPFork\Handler;

/**
 * @package   SmfX
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

class ExecResultsHandler
{

    private $_beginExecute = null;

    private $_beginLoop = null;

    private $_endLoop = null;

    private $_endExecute = null;

    /**
     * @param null $beginExecute
     * @return $this
     */
    public function setBeginExecute($beginExecute)
    {
        $this->_beginExecute = $beginExecute;
        return $this;
    }

    /**
     * @return null
     */
    public function getBeginExecute()
    {
        return $this->_beginExecute;
    }

    /**
     * @param null $beginLoop
     * @return $this
     */
    public function setBeginLoop($beginLoop)
    {
        $this->_beginLoop = $beginLoop;
        return $this;
    }

    /**
     * @return null
     */
    public function getBeginLoop()
    {
        return $this->_beginLoop;
    }

    /**
     * @param null $endExecute
     * @return $this
     */
    public function setEndExecute($endExecute)
    {
        $this->_endExecute = $endExecute;
        return $this;
    }

    /**
     * @return null
     */
    public function getEndExecute()
    {
        return $this->_endExecute;
    }

    /**
     * @param null $endLoop
     * @return $this
     */
    public function setEndLoop($endLoop)
    {
        $this->_endLoop = $endLoop;
        return $this;
    }

    /**
     * @return null
     */
    public function getEndLoop()
    {
        return $this->_endLoop;
    }


}