<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Handle\Verification;

use Eloquent\Phony\Assertion\Recorder\AssertionRecorder;
use Eloquent\Phony\Assertion\Renderer\AssertionRenderer;
use Eloquent\Phony\Event\EventCollection;
use Eloquent\Phony\Feature\FeatureDetector;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Stub\Factory\StubFactory;
use Eloquent\Phony\Stub\Factory\StubVerifierFactory;
use Eloquent\Phony\Test\TestClassH;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;
use ReflectionProperty;

class VerificationHandleTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->state = (object) array(
            'stubs' => (object) array(),
            'defaultAnswerCallback' => 'Eloquent\Phony\Stub\Stub::returnsNullAnswerCallback',
            'isRecording' => true,
            'label' => 'label',
        );
        $this->isFull = true;
        $this->stubFactory = new StubFactory();
        $this->stubVerifierFactory = new StubVerifierFactory();
        $this->assertionRenderer = new AssertionRenderer();
        $this->assertionRecorder = new AssertionRecorder();
        $this->invoker = new Invoker();

        $this->featureDetector = FeatureDetector::instance();
    }

    protected function setUpWith($className, $mockClassName = null)
    {
        $this->mockBuilder = new MockBuilder($className);
        $this->mockBuilder->named($mockClassName);
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partial();
        $this->subject = new VerificationHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $this->className = $this->class->getName();

        $handleProperty = $this->class->getProperty('_handle');
        $handleProperty->setAccessible(true);
        $handle = $handleProperty->getValue($this->mock);

        $stateProperty = new ReflectionProperty('Eloquent\Phony\Mock\Handle\AbstractHandle', 'state');
        $stateProperty->setAccessible(true);
        $stateProperty->setValue($handle, $this->state);
    }

    public function testConstructor()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');

        $this->assertSame($this->mock, $this->subject->mock());
        $this->assertInstanceOf('ReflectionClass', $this->subject->clazz());
        $this->assertSame($this->className, $this->subject->clazz()->getName());
        $this->assertSame($this->className, $this->subject->className());
        $this->assertSame($this->state->stubs, $this->subject->stubs());
        $this->assertSame($this->state->label, $this->subject->label());
        $this->assertTrue($this->subject->isAdaptable());
        $this->assertSame($this->stubFactory, $this->subject->stubFactory());
        $this->assertSame($this->stubVerifierFactory, $this->subject->stubVerifierFactory());
        $this->assertSame($this->assertionRenderer, $this->subject->assertionRenderer());
        $this->assertSame($this->assertionRecorder, $this->subject->assertionRecorder());
        $this->assertSame($this->invoker, $this->subject->invoker());
    }

    public function testConstructorDefaults()
    {
        $this->mockBuilder = new MockBuilder('Eloquent\Phony\Test\TestClassB');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partial();
        $this->subject = new VerificationHandle($this->mock);

        $this->assertEquals((object) array(), $this->subject->stubs());
        $this->assertSame(StubFactory::instance(), $this->subject->stubFactory());
        $this->assertSame(StubVerifierFactory::instance(), $this->subject->stubVerifierFactory());
        $this->assertSame(AssertionRenderer::instance(), $this->subject->assertionRenderer());
        $this->assertSame(AssertionRecorder::instance(), $this->subject->assertionRecorder());
        $this->assertSame(Invoker::instance(), $this->subject->invoker());
    }

    public function testSetLabel()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertSame($this->subject, $this->subject->setLabel(null));
        $this->assertNull($this->subject->label());
        $this->assertSame($this->subject, $this->subject->setLabel($this->state->label));
        $this->assertSame($this->state->label, $this->subject->label());
    }

    public function testSetIsAdaptable()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertTrue($this->subject->isAdaptable());
        $this->assertSame($this->subject, $this->subject->setIsAdaptable(false));
        $this->assertFalse($this->subject->isAdaptable());
    }

    public function testFull()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');

        $this->assertSame($this->subject, $this->subject->full());
        $this->assertNull($this->mock->testClassAMethodA());
        $this->assertNull($this->mock->testClassAMethodB('a', 'b'));
    }

    public function testPartial()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');

        $this->assertSame($this->subject, $this->subject->partial());
        $this->assertSame('', $this->mock->testClassAMethodA());
        $this->assertSame('ab', $this->mock->testClassAMethodB('a', 'b'));
    }

    public function testProxy()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $protectedMethod = new ReflectionMethod($this->mock, 'testClassAMethodC');
        $protectedMethod->setAccessible(true);
        $privateMethod = new ReflectionMethod($this->mock, 'testClassAMethodE');
        $privateMethod->setAccessible(true);

        $this->assertSame($this->subject, $this->subject->proxy(new TestClassH()));
        $this->assertSame('final ab', $this->mock->testClassAMethodB('a', 'b'));
        $this->assertSame('final protected ab', $protectedMethod->invoke($this->mock, 'a', 'b'));
        $this->assertSame('private ab', $privateMethod->invoke($this->mock, 'a', 'b'));
    }

    public function testSetDefaultAnswerCallback()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $callbackA = function () {};
        $callbackB = function () {};

        $this->assertSame($this->subject, $this->subject->setDefaultAnswerCallback($callbackA));
        $this->assertSame($callbackA, $this->subject->defaultAnswerCallback());
        $this->assertSame($this->subject, $this->subject->setDefaultAnswerCallback($callbackB));
        $this->assertSame($callbackB, $this->subject->defaultAnswerCallback());
    }

    public function testStub()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->stub('testClassAMethodA');

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->stub('testClassAMethodA'));
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda);
    }

    public function testStubWithMagic()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');
        $actual = $this->subject->stub('nonexistent');

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->stub('nonexistent'));
        $this->assertSame($actual, $this->subject->state()->stubs->nonexistent);
    }

    public function testStubFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->stub('nonexistent');
    }

    public function testMagicProperty()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->testClassAMethodA;

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->testClassAMethodA);
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda);
    }

    public function testMagicPropertyFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->nonexistent;
    }

    public function testSpy()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->spy('testClassAMethodA');

        $this->assertInstanceOf('Eloquent\Phony\Spy\Spy', $actual);
        $this->assertSame($actual, $this->subject->spy('testClassAMethodA'));
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda->spy());
    }

    public function testCheckNoInteraction()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertTrue((boolean) $this->subject->checkNoInteraction());

        $this->mock->testClassAMethodA();

        $this->assertFalse((boolean) $this->subject->checkNoInteraction());
    }

    public function testNoInteraction()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertEquals(new EventCollection(), $this->subject->noInteraction());
    }

    public function testNoInteractionFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA', 'PhonyMockVerificationNoInteraction');
        $this->mock->testClassAMethodA('a', 'b');
        $this->mock->testClassAMethodB('c', 'd');
        $this->mock->testClassAMethodA('e', 'f');
        $expected = <<<'EOD'
Expected no interaction with TestClassA[label]. Calls:
    - TestClassA[label]->testClassAMethodA("a", "b")
    - TestClassA[label]->testClassAMethodB("c", "d")
    - TestClassA[label]->testClassAMethodA("e", "f")
EOD;

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException', $expected);
        $this->subject->noInteraction();
    }

    public function testMagicCall()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $this->mock->testClassAMethodA();

        $this->assertSame($this->subject, $this->subject->testClassAMethodA());
    }

    public function testMagicCallFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->nonexistent();
    }

    public function testConstruct()
    {
        $this->mockBuilder = new MockBuilder('Eloquent\Phony\Test\TestClassB');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new VerificationHandle($this->mock);

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->construct('a', 'b'));
        $this->assertSame(array('a', 'b'), $this->mock->constructorArguments);
    }

    public function testConstructWith()
    {
        $this->mockBuilder = new MockBuilder('Eloquent\Phony\Test\TestClassB');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new VerificationHandle($this->mock);

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->constructWith(array('a', 'b')));
        $this->assertSame(array('a', 'b'), $this->mock->constructorArguments);
    }

    public function testConstructWithWithReferenceParameters()
    {
        $this->mockBuilder = new MockBuilder('Eloquent\Phony\Test\TestClassA');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new VerificationHandle($this->mock);
        $a = 'a';
        $b = 'b';

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->constructWith(array(&$a, &$b)));
        $this->assertSame('first', $a);
        $this->assertSame('second', $b);
    }

    public function testVerificationWithParentMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $this->subject->partial();
        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->testClassAMethodA('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->testClassAMethodA();
    }

    public function testVerificationWithTraitMethod()
    {
        if (!$this->featureDetector->isSupported('trait')) {
            $this->markTestSkipped('Requires traits.');
        }

        $this->setUpWith('Eloquent\Phony\Test\TestTraitA');
        $this->subject->partial();
        $this->mock->testClassAMethodB('a', 'b');

        $this->assertSame($this->subject, $this->subject->testClassAMethodB('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->testClassAMethodB();
    }

    public function testVerificationWithMagicMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');
        $this->subject->partial();
        $this->mock->nonexistent('a', 'b');

        $this->assertSame($this->subject, $this->subject->nonexistent('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->nonexistent();
    }

    public function testVerificationWithNoParentMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');
        $this->subject->partial();
        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->testClassAMethodA('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->testClassAMethodA();
    }

    public function testVerificationFailureWithFinalMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassF');
        $this->subject->partial();

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\FinalMethodStubException');
        $this->subject->testClassFMethodA;
    }

    public function testVerificationWithTraitFinalMethod()
    {
        if (!$this->featureDetector->isSupported('trait')) {
            $this->markTestSkipped('Requires traits.');
        }

        $this->setUpWith('Eloquent\Phony\Test\TestTraitG');
        $this->subject->partial();
        $this->mock->testTraitGMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->testTraitGMethodA('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->testTraitGMethodA();
    }

    public function testVerificationWithCustomMethod()
    {
        $this->mockBuilder = new MockBuilder(
            array(
                'static methodA' => function () {
                    return implode(func_get_args());
                },
            )
        );
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partial();
        $this->subject = new VerificationHandle($this->mock);
        $handleProperty = $this->class->getProperty('_staticHandle');
        $handleProperty->setAccessible(true);
        $handle = $handleProperty->getValue($this->mock);
        $stateProperty = new ReflectionProperty('Eloquent\Phony\Mock\Handle\AbstractHandle', 'state');
        $stateProperty->setAccessible(true);
        $stateProperty->setValue($handle, $this->subject->state());
        $this->mock->methodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->methodA('a', 'b'));

        $this->setExpectedException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->methodA();
    }

    public function testStopRecording()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');

        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->stopRecording());

        $this->mock->testClassAMethodB('a', 'b');

        $this->subject->testClassAMethodA->called();
        $this->subject->testClassAMethodB->never()->called();
    }

    public function testStartRecording()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');

        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->stopRecording());
        $this->assertSame($this->subject, $this->subject->startRecording());

        $this->mock->testClassAMethodB('a', 'b');

        $this->subject->testClassAMethodA->called();
        $this->subject->testClassAMethodB->called();
    }
}