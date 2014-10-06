<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call;

use Eloquent\Phony\Test\TestCallFactory;
use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * @covers \Eloquent\Phony\Call\Call
 */
class CallWithGeneratorsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->callFactory = new TestCallFactory();
        $this->callback = 'implode';
        $this->arguments = array('a', 'b');
        $this->calledEvent = $this->callFactory->createCalledEvent($this->callback, $this->arguments);
        $this->returnValue = 'ab';
        $this->returnedEvent = $this->callFactory->createReturnedEvent($this->returnValue);
        $this->subject = new Call($this->calledEvent, $this->returnedEvent);

        $this->events = array($this->calledEvent, $this->returnedEvent);
    }

    public function testConstructorWithGeneratedEventWithReturnEnd()
    {
        $generatedEvent = $this->callFactory->createGeneratedEvent();
        $generatorEventA = $this->callFactory->createYieldedEvent();
        $generatorEventB = $this->callFactory->createSentEvent();
        $generatorEvents = array($generatorEventA, $generatorEventB);
        $endEvent = $this->callFactory->createReturnedEvent();
        $this->subject = new Call($this->calledEvent, $generatedEvent, $generatorEvents, $endEvent);
        $this->events = array($this->calledEvent, $generatedEvent, $generatorEventA, $generatorEventB, $endEvent);

        $this->assertSame($this->calledEvent, $this->subject->calledEvent());
        $this->assertSame($generatedEvent, $this->subject->responseEvent());
        $this->assertSame($generatorEvents, $this->subject->generatorEvents());
        $this->assertSame($endEvent, $this->subject->endEvent());
        $this->assertSame($this->events, $this->subject->events());
        $this->assertTrue($this->subject->hasResponded());
        $this->assertTrue($this->subject->hasCompleted());
        $this->assertSame($this->callback, $this->subject->callback());
        $this->assertSame($this->arguments, $this->subject->arguments());
        $this->assertSame($this->calledEvent->sequenceNumber(), $this->subject->sequenceNumber());
        $this->assertEquals($this->calledEvent->time(), $this->subject->startTime());
        $this->assertInstanceOf('Generator', $this->subject->returnValue());
        $this->assertNull($this->subject->exception());
        $this->assertEquals($generatedEvent->time(), $this->subject->responseTime());
        $this->assertEquals($endEvent->time(), $this->subject->endTime());
    }

    public function testConstructorWithGeneratedEventWithThrowEnd()
    {
        $generatedEvent = $this->callFactory->createGeneratedEvent();
        $generatorEventA = $this->callFactory->createYieldedEvent();
        $generatorEventB = $this->callFactory->createSentEvent();
        $generatorEvents = array($generatorEventA, $generatorEventB);
        $exception = new RuntimeException('You done goofed.');
        $endEvent = $this->callFactory->createThrewEvent($exception);
        $this->subject = new Call($this->calledEvent, $generatedEvent, $generatorEvents, $endEvent);
        $this->events = array($this->calledEvent, $generatedEvent, $generatorEventA, $generatorEventB, $endEvent);

        $this->assertSame($this->calledEvent, $this->subject->calledEvent());
        $this->assertSame($generatedEvent, $this->subject->responseEvent());
        $this->assertSame($generatorEvents, $this->subject->generatorEvents());
        $this->assertSame($endEvent, $this->subject->endEvent());
        $this->assertSame($this->events, $this->subject->events());
        $this->assertTrue($this->subject->hasResponded());
        $this->assertTrue($this->subject->hasCompleted());
        $this->assertSame($this->callback, $this->subject->callback());
        $this->assertSame($this->arguments, $this->subject->arguments());
        $this->assertSame($this->calledEvent->sequenceNumber(), $this->subject->sequenceNumber());
        $this->assertEquals($this->calledEvent->time(), $this->subject->startTime());
        $this->assertInstanceOf('Generator', $this->subject->returnValue());
        $this->assertSame($exception, $this->subject->exception());
        $this->assertEquals($generatedEvent->time(), $this->subject->responseTime());
        $this->assertEquals($endEvent->time(), $this->subject->endTime());
    }

    public function testConstructorWithGeneratedEventWithoutEnd()
    {
        $generatedEvent = $this->callFactory->createGeneratedEvent();
        $generatorEventA = $this->callFactory->createYieldedEvent();
        $generatorEventB = $this->callFactory->createSentEvent();
        $generatorEvents = array($generatorEventA, $generatorEventB);
        $this->subject = new Call($this->calledEvent, $generatedEvent, $generatorEvents);
        $this->events = array($this->calledEvent, $generatedEvent, $generatorEventA, $generatorEventB);

        $this->assertSame($this->calledEvent, $this->subject->calledEvent());
        $this->assertSame($generatedEvent, $this->subject->responseEvent());
        $this->assertSame($generatorEvents, $this->subject->generatorEvents());
        $this->assertNull($this->subject->endEvent());
        $this->assertSame($this->events, $this->subject->events());
        $this->assertTrue($this->subject->hasResponded());
        $this->assertFalse($this->subject->hasCompleted());
        $this->assertSame($this->callback, $this->subject->callback());
        $this->assertSame($this->arguments, $this->subject->arguments());
        $this->assertSame($this->calledEvent->sequenceNumber(), $this->subject->sequenceNumber());
        $this->assertEquals($this->calledEvent->time(), $this->subject->startTime());
        $this->assertInstanceOf('Generator', $this->subject->returnValue());
        $this->assertNull($this->subject->exception());
        $this->assertEquals($generatedEvent->time(), $this->subject->responseTime());
        $this->assertNull($this->subject->endTime());
    }

    public function testSetResponseEventWithGeneratedEvent()
    {
        $generatedEvent = $this->callFactory->createGeneratedEvent();
        $this->subject = new Call($this->calledEvent);
        $this->subject->setResponseEvent($generatedEvent);

        $this->assertSame($generatedEvent, $this->subject->responseEvent());
        $this->assertNull($this->subject->endEvent());
    }

    public function testAddGeneratorEvent()
    {
        $generatedEvent = $this->callFactory->createGeneratedEvent();
        $generatorEventA = $this->callFactory->createYieldedEvent();
        $generatorEventB = $this->callFactory->createSentEvent();
        $this->subject = new Call($this->calledEvent, $generatedEvent);
        $this->subject->addGeneratorEvent($generatorEventA);
        $this->subject->addGeneratorEvent($generatorEventB);
        $generatorEvents = array($generatorEventA, $generatorEventB);

        $this->assertSame($generatorEvents, $this->subject->generatorEvents());
    }

    public function testAddGeneratorEventFailureAlreadyCompleted()
    {
        $this->setExpectedException('InvalidArgumentException', 'Call already completed.');
        $this->subject->addGeneratorEvent($this->callFactory->createSentEvent('e'));
    }
}