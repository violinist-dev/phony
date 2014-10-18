<?php

/**
 * A mock class generated by Phony.
 *
 * @uses \DateTime
 *
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with the Phony source code.
 *
 * @link https://github.com/eloquent/phony
 */
class MockGeneratorDateTimeHhvm
extends \DateTime
implements \Eloquent\Phony\Mock\MockInterface
{
    /**
     * Inherited method 'createFromFormat'.
     *
     * @uses \DateTime::createFromFormat()
     *
     * @param mixed $a0 Was 'format'.
     * @param mixed $a1 Was 'time'.
     * @param mixed $a2 Was 'timezone'.
     */
    public static function createFromFormat(
        $a0,
        $a1,
        $a2 = null
    ) {
        if (isset(self::$_staticStubs[__FUNCTION__])) {
            return self::$_staticStubs[__FUNCTION__]
                ->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'getLastErrors'.
     *
     * @uses \DateTime::getLastErrors()
     */
    public static function getLastErrors()
    {
        if (isset(self::$_staticStubs[__FUNCTION__])) {
            return self::$_staticStubs[__FUNCTION__]
                ->invokeWith(func_get_args());
        }
    }

    /**
     * Construct a mock.
     */
    public function __construct()
    {
    }

    /**
     * Inherited method '__debugInfo'.
     *
     * @uses \DateTime::__debugInfo()
     */
    public function __debugInfo()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method '__sleep'.
     *
     * @uses \DateTime::__sleep()
     */
    public function __sleep()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method '__wakeup'.
     *
     * @uses \DateTime::__wakeup()
     */
    public function __wakeup()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'add'.
     *
     * @uses \DateTime::add()
     *
     * @param mixed $a0 Was 'interval'.
     */
    public function add(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'diff'.
     *
     * @uses \DateTime::diff()
     *
     * @param mixed $a0 Was 'datetime2'.
     * @param mixed $a1 Was 'absolute'.
     */
    public function diff(
        $a0,
        $a1 = false
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'format'.
     *
     * @uses \DateTime::format()
     *
     * @param mixed $a0 Was 'format'.
     */
    public function format(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'getOffset'.
     *
     * @uses \DateTime::getOffset()
     */
    public function getOffset()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'getTimestamp'.
     *
     * @uses \DateTime::getTimestamp()
     */
    public function getTimestamp()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'getTimezone'.
     *
     * @uses \DateTime::getTimezone()
     */
    public function getTimezone()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'modify'.
     *
     * @uses \DateTime::modify()
     *
     * @param mixed $a0 Was 'modify'.
     */
    public function modify(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'setDate'.
     *
     * @uses \DateTime::setDate()
     *
     * @param mixed $a0 Was 'year'.
     * @param mixed $a1 Was 'month'.
     * @param mixed $a2 Was 'day'.
     */
    public function setDate(
        $a0,
        $a1,
        $a2
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'setISODate'.
     *
     * @uses \DateTime::setISODate()
     *
     * @param mixed $a0 Was 'year'.
     * @param mixed $a1 Was 'week'.
     * @param mixed $a2 Was 'day'.
     */
    public function setISODate(
        $a0,
        $a1,
        $a2 = 1
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'setTime'.
     *
     * @uses \DateTime::setTime()
     *
     * @param mixed $a0 Was 'hour'.
     * @param mixed $a1 Was 'minute'.
     * @param mixed $a2 Was 'second'.
     */
    public function setTime(
        $a0,
        $a1,
        $a2 = 0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'setTimestamp'.
     *
     * @uses \DateTime::setTimestamp()
     *
     * @param mixed $a0 Was 'unixtimestamp'.
     */
    public function setTimestamp(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'setTimezone'.
     *
     * @uses \DateTime::setTimezone()
     *
     * @param mixed $a0 Was 'timezone'.
     */
    public function setTimezone(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Inherited method 'sub'.
     *
     * @uses \DateTime::sub()
     *
     * @param mixed $a0 Was 'interval'.
     */
    public function sub(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(func_get_args());
        }
    }

    /**
     * Call a static parent method.
     *
     * @param string $name The method name.
     * @param array<integer,mixed> The arguments.
     */
    private static function _callParentStatic($name, array $arguments)
    {
        return call_user_func_array(
            array(__CLASS__, 'parent::' . $name),
            $arguments
        );
    }

    /**
     * Call a parent method.
     *
     * @param string $name The method name.
     * @param array<integer,mixed> The arguments.
     */
    private function _callParent($name, array $arguments)
    {
        return call_user_func_array(
            array($this, 'parent::' . $name),
            $arguments
        );
    }

    private static $_staticStubs = array();
    private $_stubs = array();
    private $_mockId;
}
