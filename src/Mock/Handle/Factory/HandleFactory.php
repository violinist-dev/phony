<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Handle\Factory;

use Eloquent\Phony\Assertion\Recorder\AssertionRecorder;
use Eloquent\Phony\Assertion\Recorder\AssertionRecorderInterface;
use Eloquent\Phony\Assertion\Renderer\AssertionRenderer;
use Eloquent\Phony\Assertion\Renderer\AssertionRendererInterface;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Invocation\InvokerInterface;
use Eloquent\Phony\Mock\Exception\InvalidMockClassException;
use Eloquent\Phony\Mock\Exception\InvalidMockException;
use Eloquent\Phony\Mock\Exception\MockExceptionInterface;
use Eloquent\Phony\Mock\Exception\NonMockClassException;
use Eloquent\Phony\Mock\Handle\HandleInterface;
use Eloquent\Phony\Mock\Handle\InstanceHandleInterface;
use Eloquent\Phony\Mock\Handle\Stubbing\InstanceStubbingHandleInterface;
use Eloquent\Phony\Mock\Handle\Stubbing\StaticStubbingHandle;
use Eloquent\Phony\Mock\Handle\Stubbing\StaticStubbingHandleInterface;
use Eloquent\Phony\Mock\Handle\Stubbing\StubbingHandle;
use Eloquent\Phony\Mock\Handle\Verification\InstanceVerificationHandleInterface;
use Eloquent\Phony\Mock\Handle\Verification\StaticVerificationHandle;
use Eloquent\Phony\Mock\Handle\Verification\StaticVerificationHandleInterface;
use Eloquent\Phony\Mock\Handle\Verification\VerificationHandle;
use Eloquent\Phony\Mock\MockInterface;
use Eloquent\Phony\Stub\Factory\StubFactory;
use Eloquent\Phony\Stub\Factory\StubFactoryInterface;
use Eloquent\Phony\Stub\Factory\StubVerifierFactory;
use Eloquent\Phony\Stub\Factory\StubVerifierFactoryInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Creates handles.
 */
class HandleFactory implements HandleFactoryInterface
{
    /**
     * Get the static instance of this factory.
     *
     * @return HandleFactoryInterface The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                StubFactory::instance(),
                StubVerifierFactory::instance(),
                AssertionRenderer::instance(),
                AssertionRecorder::instance(),
                Invoker::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new handle factory.
     *
     * @param StubFactoryInterface         $stubFactory         The stub factory to use.
     * @param StubVerifierFactoryInterface $stubVerifierFactory The stub verifier factory to use.
     * @param AssertionRendererInterface   $assertionRenderer   The assertion renderer to use.
     * @param AssertionRecorderInterface   $assertionRecorder   The assertion recorder to use.
     * @param InvokerInterface             $invoker             The invoker to use.
     */
    public function __construct(
        StubFactoryInterface $stubFactory,
        StubVerifierFactoryInterface $stubVerifierFactory,
        AssertionRendererInterface $assertionRenderer,
        AssertionRecorderInterface $assertionRecorder,
        InvokerInterface $invoker
    ) {
        $this->stubFactory = $stubFactory;
        $this->stubVerifierFactory = $stubVerifierFactory;
        $this->assertionRenderer = $assertionRenderer;
        $this->assertionRecorder = $assertionRecorder;
        $this->invoker = $invoker;
    }

    /**
     * Create a new stubbing handle.
     *
     * @param MockInterface|InstanceHandleInterface $mock  The mock.
     * @param string|null                           $label The label.
     *
     * @return InstanceStubbingHandleInterface The newly created handle.
     * @throws MockExceptionInterface          If the supplied mock is invalid.
     */
    public function createStubbing($mock, $label = null)
    {
        if ($mock instanceof InstanceStubbingHandleInterface) {
            return $mock;
        }

        if ($mock instanceof InstanceHandleInterface) {
            $mock = $mock->mock();
        }

        if (!$mock instanceof MockInterface) {
            throw new InvalidMockException($mock);
        }

        $class = new ReflectionClass($mock);

        $handleProperty = $class->getProperty('_handle');
        $handleProperty->setAccessible(true);

        if ($handle = $handleProperty->getValue($mock)) {
            return $handle;
        }

        $handle = new StubbingHandle(
            $mock,
            (object) array(
                'defaultAnswerCallback' =>
                    'Eloquent\Phony\Stub\Stub::returnsEmptyAnswerCallback',
                'stubs' => (object) array(),
                'isRecording' => true,
                'label' => $label,
            ),
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $handleProperty->setValue($mock, $handle);

        return $handle;
    }

    /**
     * Create a new verification handle.
     *
     * @param MockInterface|InstanceHandleInterface $mock The mock.
     *
     * @return InstanceVerificationHandleInterface The newly created handle.
     * @throws MockExceptionInterface              If the supplied mock is invalid.
     */
    public function createVerification($mock)
    {
        if ($mock instanceof InstanceVerificationHandleInterface) {
            return $mock;
        }

        $stubbingHandle = $this->createStubbing($mock);

        return new VerificationHandle(
            $stubbingHandle->mock(),
            $stubbingHandle->state(),
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );
    }

    /**
     * Create a new static stubbing handle.
     *
     * @param MockInterface|HandleInterface|ReflectionClass|string $class The class.
     *
     * @return StaticStubbingHandleInterface The newly created handle.
     * @throws MockExceptionInterface        If the supplied class name is not a mock class.
     */
    public function createStubbingStatic($class)
    {
        if ($class instanceof StaticStubbingHandleInterface) {
            return $class;
        }

        if ($class instanceof HandleInterface) {
            $class = $class->clazz();
        } elseif ($class instanceof MockInterface) {
            $class = new ReflectionClass($class);
        } elseif (is_string($class)) {
            try {
                $class = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                throw new NonMockClassException($class, $e);
            }
        } elseif (!$class instanceof ReflectionClass) {
            throw new InvalidMockClassException($class);
        }

        if (!$class->isSubclassOf('Eloquent\Phony\Mock\MockInterface')) {
            throw new NonMockClassException($class->getName());
        }

        $handleProperty = $class->getProperty('_staticHandle');
        $handleProperty->setAccessible(true);

        if ($handle = $handleProperty->getValue(null)) {
            return $handle;
        }

        $handle = new StaticStubbingHandle(
            $class,
            (object) array(
                'defaultAnswerCallback' =>
                    'Eloquent\Phony\Stub\Stub::forwardsAnswerCallback',
                'stubs' => (object) array(),
                'isRecording' => true,
            ),
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $handleProperty->setValue(null, $handle);

        return $handle;
    }

    /**
     * Create a new static verification handle.
     *
     * @param MockInterface|HandleInterface|ReflectionClass|string $class The class.
     *
     * @return StaticVerificationHandleInterface The newly created handle.
     * @throws MockExceptionInterface            If the supplied class name is not a mock class.
     */
    public function createVerificationStatic($class)
    {
        if ($class instanceof StaticVerificationHandleInterface) {
            return $class;
        }

        $stubbingHandle = $this->createStubbingStatic($class);

        return new StaticVerificationHandle(
            $stubbingHandle->clazz(),
            $stubbingHandle->state(),
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );
    }

    private static $instance;
    private $mockFactory;
    private $stubVerifierFactory;
    private $assertionRenderer;
    private $assertionRecorder;
    private $invoker;
}
