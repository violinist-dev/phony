<?php

/**
 * A mock class generated by Phony.
 *
 * @uses \ReflectionClass
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
class MockGeneratorReflectionClassTraits
extends \ReflectionClass
implements \Eloquent\Phony\Mock\MockInterface
{
    /**
     * Inherited method 'export'.
     *
     * @uses \ReflectionClass::export()
     *
     * @param mixed $a0 Was 'argument'.
     * @param mixed $a1 Was 'return'.
     */
    public static function export(
        $a0,
        $a1 = null
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;
        if ($argumentCount > 1) $arguments[] = $a1;

        for ($i = 2; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset(self::$_staticStubs[__FUNCTION__])) {
            return self::$_staticStubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Construct a mock.
     */
    public function __construct()
    {
    }

    /**
     * Inherited method '__toString'.
     *
     * @uses \ReflectionClass::__toString()
     */
    public function __toString()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getConstant'.
     *
     * @uses \ReflectionClass::getConstant()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function getConstant(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getConstants'.
     *
     * @uses \ReflectionClass::getConstants()
     */
    public function getConstants()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getConstructor'.
     *
     * @uses \ReflectionClass::getConstructor()
     */
    public function getConstructor()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getDefaultProperties'.
     *
     * @uses \ReflectionClass::getDefaultProperties()
     */
    public function getDefaultProperties()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getDocComment'.
     *
     * @uses \ReflectionClass::getDocComment()
     */
    public function getDocComment()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getEndLine'.
     *
     * @uses \ReflectionClass::getEndLine()
     */
    public function getEndLine()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getExtension'.
     *
     * @uses \ReflectionClass::getExtension()
     */
    public function getExtension()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getExtensionName'.
     *
     * @uses \ReflectionClass::getExtensionName()
     */
    public function getExtensionName()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getFileName'.
     *
     * @uses \ReflectionClass::getFileName()
     */
    public function getFileName()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getInterfaceNames'.
     *
     * @uses \ReflectionClass::getInterfaceNames()
     */
    public function getInterfaceNames()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getInterfaces'.
     *
     * @uses \ReflectionClass::getInterfaces()
     */
    public function getInterfaces()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getMethod'.
     *
     * @uses \ReflectionClass::getMethod()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function getMethod(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getMethods'.
     *
     * @uses \ReflectionClass::getMethods()
     *
     * @param mixed $a0 Was 'filter'.
     */
    public function getMethods(
        $a0 = null
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getModifiers'.
     *
     * @uses \ReflectionClass::getModifiers()
     */
    public function getModifiers()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getName'.
     *
     * @uses \ReflectionClass::getName()
     */
    public function getName()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getNamespaceName'.
     *
     * @uses \ReflectionClass::getNamespaceName()
     */
    public function getNamespaceName()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getParentClass'.
     *
     * @uses \ReflectionClass::getParentClass()
     */
    public function getParentClass()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getProperties'.
     *
     * @uses \ReflectionClass::getProperties()
     *
     * @param mixed $a0 Was 'filter'.
     */
    public function getProperties(
        $a0 = null
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getProperty'.
     *
     * @uses \ReflectionClass::getProperty()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function getProperty(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getShortName'.
     *
     * @uses \ReflectionClass::getShortName()
     */
    public function getShortName()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getStartLine'.
     *
     * @uses \ReflectionClass::getStartLine()
     */
    public function getStartLine()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getStaticProperties'.
     *
     * @uses \ReflectionClass::getStaticProperties()
     */
    public function getStaticProperties()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getStaticPropertyValue'.
     *
     * @uses \ReflectionClass::getStaticPropertyValue()
     *
     * @param mixed $a0 Was 'name'.
     * @param mixed $a1 Was 'default'.
     */
    public function getStaticPropertyValue(
        $a0,
        $a1 = null
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;
        if ($argumentCount > 1) $arguments[] = $a1;

        for ($i = 2; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getTraitAliases'.
     *
     * @uses \ReflectionClass::getTraitAliases()
     */
    public function getTraitAliases()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getTraitNames'.
     *
     * @uses \ReflectionClass::getTraitNames()
     */
    public function getTraitNames()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'getTraits'.
     *
     * @uses \ReflectionClass::getTraits()
     */
    public function getTraits()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'hasConstant'.
     *
     * @uses \ReflectionClass::hasConstant()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function hasConstant(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'hasMethod'.
     *
     * @uses \ReflectionClass::hasMethod()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function hasMethod(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'hasProperty'.
     *
     * @uses \ReflectionClass::hasProperty()
     *
     * @param mixed $a0 Was 'name'.
     */
    public function hasProperty(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'implementsInterface'.
     *
     * @uses \ReflectionClass::implementsInterface()
     *
     * @param mixed $a0 Was 'interface'.
     */
    public function implementsInterface(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'inNamespace'.
     *
     * @uses \ReflectionClass::inNamespace()
     */
    public function inNamespace()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isAbstract'.
     *
     * @uses \ReflectionClass::isAbstract()
     */
    public function isAbstract()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isCloneable'.
     *
     * @uses \ReflectionClass::isCloneable()
     */
    public function isCloneable()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isFinal'.
     *
     * @uses \ReflectionClass::isFinal()
     */
    public function isFinal()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isInstance'.
     *
     * @uses \ReflectionClass::isInstance()
     *
     * @param mixed $a0 Was 'object'.
     */
    public function isInstance(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isInstantiable'.
     *
     * @uses \ReflectionClass::isInstantiable()
     */
    public function isInstantiable()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isInterface'.
     *
     * @uses \ReflectionClass::isInterface()
     */
    public function isInterface()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isInternal'.
     *
     * @uses \ReflectionClass::isInternal()
     */
    public function isInternal()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isIterateable'.
     *
     * @uses \ReflectionClass::isIterateable()
     */
    public function isIterateable()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isSubclassOf'.
     *
     * @uses \ReflectionClass::isSubclassOf()
     *
     * @param mixed $a0 Was 'class'.
     */
    public function isSubclassOf(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isTrait'.
     *
     * @uses \ReflectionClass::isTrait()
     */
    public function isTrait()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'isUserDefined'.
     *
     * @uses \ReflectionClass::isUserDefined()
     */
    public function isUserDefined()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'newInstance'.
     *
     * @uses \ReflectionClass::newInstance()
     *
     * @param mixed $a0 Was 'args'.
     */
    public function newInstance(
        $a0
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'newInstanceArgs'.
     *
     * @uses \ReflectionClass::newInstanceArgs()
     *
     * @param array $a0 Was 'args'.
     */
    public function newInstanceArgs(
        array $a0 = null
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;

        for ($i = 1; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'newInstanceWithoutConstructor'.
     *
     * @uses \ReflectionClass::newInstanceWithoutConstructor()
     */
    public function newInstanceWithoutConstructor()
    {
        $argumentCount = func_num_args();
        $arguments = array();

        for ($i = 0; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Inherited method 'setStaticPropertyValue'.
     *
     * @uses \ReflectionClass::setStaticPropertyValue()
     *
     * @param mixed $a0 Was 'name'.
     * @param mixed $a1 Was 'value'.
     */
    public function setStaticPropertyValue(
        $a0,
        $a1
    ) {
        $argumentCount = func_num_args();
        $arguments = array();

        if ($argumentCount > 0) $arguments[] = $a0;
        if ($argumentCount > 1) $arguments[] = $a1;

        for ($i = 2; $i < $argumentCount; $i++) {
            $arguments[] = func_get_arg($i);
        }

        if (isset($this->_stubs[__FUNCTION__])) {
            return $this->_stubs[__FUNCTION__]->invokeWith(
                new \Eloquent\Phony\Call\Argument\Arguments($arguments)
            );
        }
    }

    /**
     * Call a static parent method.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private static function _callParentStatic(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        return \call_user_func_array(
            array(__CLASS__, 'parent::' . $name),
            $arguments->all()
        );
    }

    /**
     * Call a parent method.
     *
     * @param string                                           $name      The method name.
     * @param \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments The arguments.
     */
    private function _callParent(
        $name,
        \Eloquent\Phony\Call\Argument\ArgumentsInterface $arguments
    ) {
        return \call_user_func_array(
            array($this, 'parent::' . $name),
            $arguments->all()
        );
    }

    private static $_staticStubs = array();
    private static $_magicStaticStubs = array();
    private $_stubs = array();
    private $_magicStubs = array();
    private $_mockId;
}
