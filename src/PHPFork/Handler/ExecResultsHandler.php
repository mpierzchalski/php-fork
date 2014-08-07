<?php

namespace PHPFork\Handler;
use PHPFork\Subscriber;

/**
 * @package   php-fork
 * @author    Michał Pierzchalski <michal.pierzchalski@gmail.com>
 * @license   MIT
 */

class ExecResultsHandler
{

    /**
     * @var array
     */
    private $beginExecute = [];

    /**
     * @var array
     */
    private $endExecute = [];

    /**
     * @param \PHPFork\Subscriber $subscriber
     * @param mixed               $beginExecute
     * @return $this
     */
    public function setBeginExecute(Subscriber $subscriber, $beginExecute)
    {
        $this->beginExecute[get_class($subscriber)] = $beginExecute;
        return $this;
    }

    /**
     * @param Subscriber $subscriber
     * @return mixed
     */
    public function getBeginExecute(Subscriber $subscriber)
    {
        $class = get_class($subscriber);
        if (isset($this->beginExecute[$class])) {
            return $this->beginExecute[$class];
        }
        return null;
    }

    /**
     * @param Subscriber $subscriber
     * @param mixed      $endExecute
     * @return $this
     */
    public function setEndExecute(Subscriber $subscriber, $endExecute)
    {
        $this->endExecute[get_class($subscriber)] = $endExecute;
        return $this;
    }

    /**
     * @param Subscriber $subscriber
     * @return null
     */
    public function getEndExecute(Subscriber $subscriber)
    {
        $class = get_class($subscriber);
        if (isset($this->endExecute[$class])) {
            return $this->endExecute[$class];
        }
        return null;
    }

}