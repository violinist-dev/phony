<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder;

use Eloquent\Phony\Call\Argument\Arguments;
use Eloquent\Phony\Call\Argument\ArgumentsInterface;
use Eloquent\Phony\Feature\FeatureDetector;
use Eloquent\Phony\Feature\FeatureDetectorInterface;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\InvocableInspectorInterface;
use Eloquent\Phony\Mock\Builder\Definition\MockDefinition;
use Eloquent\Phony\Mock\Builder\Definition\MockDefinitionInterface;
use Eloquent\Phony\Mock\Exception\AnonymousClassException;
use Eloquent\Phony\Mock\Exception\FinalClassException;
use Eloquent\Phony\Mock\Exception\FinalizedMockException;
use Eloquent\Phony\Mock\Exception\InvalidClassNameException;
use Eloquent\Phony\Mock\Exception\InvalidDefinitionException;
use Eloquent\Phony\Mock\Exception\InvalidTypeException;
use Eloquent\Phony\Mock\Exception\MockExceptionInterface;
use Eloquent\Phony\Mock\Exception\MultipleInheritanceException;
use Eloquent\Phony\Mock\Factory\MockFactory;
use Eloquent\Phony\Mock\Factory\MockFactoryInterface;
use Eloquent\Phony\Mock\Generator\MockGenerator;
use Eloquent\Phony\Mock\Generator\MockGeneratorInterface;
use Eloquent\Phony\Mock\MockInterface;
use Eloquent\Phony\Mock\Proxy\Factory\ProxyFactory;
use Eloquent\Phony\Mock\Proxy\Factory\ProxyFactoryInterface;
use Eloquent\Phony\Reflection\FunctionSignatureInspector;
use Eloquent\Phony\Reflection\FunctionSignatureInspectorInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Builds mock classes.
 */
class MockBuilder implements MockBuilderInterface
{
    /**
     * The regular expression used to validate symbol names.
     */
    const SYMBOL_PATTERN = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*';

    /**
     * Construct a new mock builder.
     *
     * Each value in `$types` can be either a class name, or an ad hoc mock
     * definition. If only a single type is being mocked, the class name or
     * definition can be passed without being wrapped in an array.
     *
     * @param mixed                                    $types              The types to mock.
     * @param MockFactoryInterface|null                $factory            The factory to use.
     * @param ProxyFactoryInterface|null               $proxyFactory       The proxy factory to use.
     * @param FunctionSignatureInspectorInterface|null $signatureInspector The function signature inspector to use.
     * @param InvocableInspectorInterface|null         $invocableInspector The invocable inspector.
     * @param FeatureDetectorInterface|null            $featureDetector    The feature detector to use.
     *
     * @throws MockExceptionInterface If invalid input is supplied.
     */
    public function __construct(
        $types = null,
        MockFactoryInterface $factory = null,
        ProxyFactoryInterface $proxyFactory = null,
        FunctionSignatureInspectorInterface $signatureInspector = null,
        InvocableInspectorInterface $invocableInspector = null,
        FeatureDetectorInterface $featureDetector = null
    ) {
        if (null === $factory) {
            $factory = MockFactory::instance();
        }
        if (null === $proxyFactory) {
            $proxyFactory = ProxyFactory::instance();
        }
        if (null === $signatureInspector) {
            $signatureInspector = FunctionSignatureInspector::instance();
        }
        if (null === $invocableInspector) {
            $invocableInspector = InvocableInspector::instance();
        }
        if (null === $featureDetector) {
            $featureDetector = FeatureDetector::instance();
        }

        $this->isTraitSupported = $featureDetector->isSupported('trait');
        $this->isAnonymousClassSupported =
            $featureDetector->isSupported('class.anonymous');

        $this->factory = $factory;
        $this->proxyFactory = $proxyFactory;
        $this->signatureInspector = $signatureInspector;
        $this->invocableInspector = $invocableInspector;
        $this->featureDetector = $featureDetector;

        $this->types = array();
        $this->parentClassName = null;
        $this->customMethods = array();
        $this->customProperties = array();
        $this->customStaticMethods = array();
        $this->customStaticProperties = array();
        $this->customConstants = array();
        $this->isFinalized = false;

        if (null !== $types) {
            $this->like($types);
        }
    }

    /**
     * Get the factory.
     *
     * @return MockFactoryInterface The factory.
     */
    public function factory()
    {
        return $this->factory;
    }

    /**
     * Get the proxy factory.
     *
     * @return ProxyFactoryInterface The proxy factory.
     */
    public function proxyFactory()
    {
        return $this->proxyFactory;
    }

    /**
     * Get the function signature inspector.
     *
     * @return FunctionSignatureInspectorInterface The function signature inspector.
     */
    public function signatureInspector()
    {
        return $this->signatureInspector;
    }

    /**
     * Get the invocable inspector.
     *
     * @return InvocableInspectorInterface The invocable inspector.
     */
    public function invocableInspector()
    {
        return $this->invocableInspector;
    }

    /**
     * Get the feature detector.
     *
     * @return FeatureDetectorInterface The feature detector.
     */
    public function featureDetector()
    {
        return $this->featureDetector;
    }

    /**
     * Get the types.
     *
     * @return array<string,ReflectionClass> The types.
     */
    public function types()
    {
        return $this->types;
    }

    /**
     * Add classes, interfaces, or traits.
     *
     * Each value in `$types` can be either a class name, or an ad hoc mock
     * definition. If only a single type is being mocked, the class name or
     * definition can be passed without being wrapped in an array.
     *
     * @param mixed $type A type, or types to add.
     * @param mixed ...$types Additional types to add.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If invalid input is supplied, or this builder is already finalized.
     */
    public function like($type)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $types = array();

        foreach (func_get_args() as $type) {
            if (is_array($type)) {
                if ($type) {
                    if (array_values($type) === $type) {
                        $types = array_merge($types, $type);
                    } else {
                        $types[] = $type;
                    }
                }
            } else {
                $types[] = $type;
            }
        }

        $toAdd = array();

        if (null === $this->parentClassName) {
            $parentClassNames = array();
        } else {
            $parentClassNames = array($this->parentClassName);
        }

        $parentClassName = null;
        $definitions = array();

        foreach ($types as $type) {
            if (is_string($type)) {
                try {
                    $type = new ReflectionClass($type);
                } catch (ReflectionException $e) {
                    throw new InvalidTypeException($type, $e);
                }
            } elseif (is_array($type)) {
                foreach ($type as $name => $value) {
                    if (!is_string($name)) {
                        throw new InvalidDefinitionException($name, $value);
                    }
                }

                $definitions[] = $type;

                continue;
            } else {
                throw new InvalidTypeException($type);
            }

            // @codeCoverageIgnoreStart
            if ($this->isAnonymousClassSupported && $type->isAnonymous()) {
                throw new AnonymousClassException();
            }
            // @codeCoverageIgnoreEnd

            $isTrait = $this->isTraitSupported && $type->isTrait();

            if (!$isTrait && $type->isFinal()) {
                throw new FinalClassException($type->getName());
            }

            if (!$isTrait && !$type->isInterface()) {
                $parentClassNames[] = $parentClassName = $type->getName();
            }

            $toAdd[] = $type;
        }

        $parentClassNames = array_unique($parentClassNames);
        $parentClassCount = count($parentClassNames);

        if ($parentClassCount > 1) {
            throw new MultipleInheritanceException($parentClassNames);
        }

        foreach ($toAdd as $type) {
            $name = $type->getName();

            if (!isset($this->types[$name])) {
                $this->types[$name] = $type;
            }
        }

        if ($parentClassCount > 0) {
            $this->parentClassName = $parentClassName;
        }

        foreach ($definitions as $definition) {
            $this->define($definition);
        }

        return $this;
    }

    /**
     * Add a custom method.
     *
     * @param string        $name     The name.
     * @param callable|null $callback The callback.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function addMethod($name, $callback = null)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $this->customMethods[$name] = $callback;

        return $this;
    }

    /**
     * Add a custom property.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function addProperty($name, $value = null)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $this->customProperties[$name] = $value;

        return $this;
    }

    /**
     * Add a custom static method.
     *
     * @param string        $name     The name.
     * @param callable|null $callback The callback.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function addStaticMethod($name, $callback = null)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $this->customStaticMethods[$name] = $callback;

        return $this;
    }

    /**
     * Add a custom static property.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function addStaticProperty($name, $value = null)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $this->customStaticProperties[$name] = $value;

        return $this;
    }

    /**
     * Add a custom class constant.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function addConstant($name, $value)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $this->customConstants[$name] = $value;

        return $this;
    }

    /**
     * Set the class name.
     *
     * @param string $className|null The class name, or null to use a generated name.
     *
     * @return $this                  This builder.
     * @throws MockExceptionInterface If this builder is already finalized.
     */
    public function named($className = null)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        if (null !== $className) {
            if (
                !preg_match('/^' . static::SYMBOL_PATTERN . '$/S', $className)
            ) {
                throw new InvalidClassNameException($className);
            }
        }

        $this->className = $className;

        return $this;
    }

    /**
     * Returns true if this builder is finalized.
     *
     * @return boolean True if finalized.
     */
    public function isFinalized()
    {
        return $this->isFinalized;
    }

    /**
     * Finalize the mock builder.
     *
     * @return $this This builder.
     */
    public function finalize()
    {
        if (!$this->isFinalized) {
            $this->normalizeDefinition();
            $this->isFinalized = true;
            $this->definition = $this->buildDefinition();
        }

        return $this;
    }

    /**
     * Get the mock definitions.
     *
     * Calling this method will finalize the mock builder.
     *
     * @return MockDefinitionInterface The mock definition.
     */
    public function definition()
    {
        $this->finalize();

        return $this->definition;
    }

    /**
     * Returns true if the mock class has been built.
     *
     * @return boolean True if the mock class has been built.
     */
    public function isBuilt()
    {
        return (boolean) $this->class;
    }

    /**
     * Generate and define the mock class.
     *
     * Calling this method will finalize the mock builder.
     *
     * @param boolean $createNew True if a new class should be created even when a compatible one exists.
     *
     * @return ReflectionClass        The class.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function build($createNew = false)
    {
        if (!$this->class) {
            $this->class = $this->factory->createMockClass($this, $createNew);
        }

        return $this->class;
    }

    /**
     * Generate and define the mock class, and return the class name.
     *
     * Calling this method will finalize the mock builder.
     *
     * @param boolean $createNew True if a new class should be created even when a compatible one exists.
     *
     * @return string                 The class name.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function className($createNew = false)
    {
        return $this->build($createNew)->getName();
    }

    /**
     * Get a mock.
     *
     * This method will return the current mock, only creating a new mock if no
     * existing mock is available.
     *
     * Calling this method will finalize the mock builder.
     *
     * @return MockInterface          The mock instance.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function get()
    {
        if ($this->mock) {
            return $this->mock;
        }

        $this->mock = $this->factory->createMock($this, array());

        return $this->mock;
    }

    /**
     * Create a new mock.
     *
     * This method will always create a new mock, and will replace the current
     * mock.
     *
     * Calling this method will finalize the mock builder.
     *
     * @param mixed ...$arguments The constructor arguments.
     *
     * @return MockInterface          The mock instance.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function create()
    {
        $this->mock = $this->factory->createMock($this, func_get_args());

        return $this->mock;
    }

    /**
     * Create a new mock.
     *
     * This method will always create a new mock, and will replace the current
     * mock.
     *
     * Calling this method will finalize the mock builder.
     *
     * This method supports reference parameters.
     *
     * @param ArgumentsInterface|array|null $arguments The constructor arguments, or null to bypass the constructor.
     *
     * @return MockInterface          The mock instance.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function createWith($arguments = array())
    {
        $this->mock = $this->factory->createMock($this, $arguments);

        return $this->mock;
    }

    /**
     * Create a new full mock.
     *
     * This method will always create a new mock, and will replace the current
     * mock.
     *
     * Calling this method will finalize the mock builder.
     *
     * @return MockInterface          The mock instance.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function full()
    {
        $this->mock = $this->factory->createMock($this, null);
        $this->proxyFactory->createStubbing($this->mock)->full();

        return $this->mock;
    }

    /**
     * Get the generated source code of the mock class.
     *
     * Calling this method will finalize the mock builder.
     *
     * @param MockGeneratorInterface|null $generator The mock generator to use.
     *
     * @return string                 The source code.
     * @throws MockExceptionInterface If the mock generation fails.
     */
    public function source(MockGeneratorInterface $generator = null)
    {
        if (null === $generator) {
            $generator = MockGenerator::instance();
        }

        return $generator->generate($this->definition());
    }

    private function normalizeDefinition()
    {
        $isTraversable = false;
        $isIterator = false;

        foreach ($this->types as $type) {
            if (
                $type->implementsInterface('Iterator') ||
                $type->implementsInterface('IteratorAggregate')
            ) {
                $isIterator = true;

                break;
            }

            if ($type->implementsInterface('Traversable')) {
                $isTraversable = true;
            }
        }

        if ($isTraversable && !$isIterator) {
            $this->types = array_merge(
                array(
                    'IteratorAggregate' =>
                        new ReflectionClass('IteratorAggregate'),
                ),
                $this->types
            );
        }

        if (!$this->featureDetector->isSupported('error.exception.engine')) {
            return;
        }

        // @codeCoverageIgnoreStart

        $isThrowable = false;

        foreach ($this->types as $type) {
            if (
                $type->isSubclassOf('Exception') || $type->isSubclassOf('Error')
            ) {
                return;
            }

            $name = $type->getName();

            if ('Exception' === $name || 'Error' === $type) {
                return;
            }

            if ($type->implementsInterface('Throwable')) {
                $isThrowable = true;
            }
        }

        if ($isThrowable) {
            $this->types = array_merge(
                array('Exception' => new ReflectionClass('Exception')),
                $this->types
            );
        }
    } // @codeCoverageIgnoreEnd

    private function define($definition)
    {
        foreach ($definition as $name => $value) {
            $nameParts = explode(' ', $name);
            $name = array_pop($nameParts);
            $isStatic = in_array('static', $nameParts);
            $isFunction = in_array('function', $nameParts);
            $isProperty = in_array('var', $nameParts);
            $isConstant = in_array('const', $nameParts);

            if (!$isFunction && !$isProperty && !$isConstant) {
                if (is_object($value) && is_callable($value)) {
                    $isFunction = true;
                } else {
                    $isProperty = true;
                }
            }

            if ($isFunction) {
                if ($isStatic) {
                    $this->addStaticMethod($name, $value);
                } else {
                    $this->addMethod($name, $value);
                }
            } elseif ($isConstant) {
                $this->addConstant($name, $value);
            } else {
                if ($isStatic) {
                    $this->addStaticProperty($name, $value);
                } else {
                    $this->addProperty($name, $value);
                }
            }
        }

        return $this;
    }

    private function buildDefinition()
    {
        return new MockDefinition(
            $this->types,
            $this->customMethods,
            $this->customProperties,
            $this->customStaticMethods,
            $this->customStaticProperties,
            $this->customConstants,
            $this->className,
            $this->signatureInspector,
            $this->invocableInspector,
            $this->featureDetector
        );
    }

    private $factory;
    private $proxyFactory;
    private $signatureInspector;
    private $invocableInspector;
    private $featureDetector;
    private $isTraitSupported;
    private $isAnonymousClassSupported;
    private $types;
    private $parentClassName;
    private $customMethods;
    private $customProperties;
    private $customStaticMethods;
    private $customStaticProperties;
    private $customConstants;
    private $className;
    private $isFinalized;
    private $definition;
    private $class;
    private $mock;
}
