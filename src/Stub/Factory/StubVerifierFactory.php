<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Factory;

use Eloquent\Phony\Assertion\Recorder\AssertionRecorder;
use Eloquent\Phony\Assertion\Recorder\AssertionRecorderInterface;
use Eloquent\Phony\Assertion\Renderer\AssertionRenderer;
use Eloquent\Phony\Assertion\Renderer\AssertionRendererInterface;
use Eloquent\Phony\Call\Factory\CallVerifierFactory;
use Eloquent\Phony\Call\Factory\CallVerifierFactoryInterface;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\InvocableInspectorInterface;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Invocation\InvokerInterface;
use Eloquent\Phony\Matcher\Factory\MatcherFactory;
use Eloquent\Phony\Matcher\Factory\MatcherFactoryInterface;
use Eloquent\Phony\Matcher\Verification\MatcherVerifier;
use Eloquent\Phony\Matcher\Verification\MatcherVerifierInterface;
use Eloquent\Phony\Spy\Factory\SpyFactory;
use Eloquent\Phony\Spy\Factory\SpyFactoryInterface;
use Eloquent\Phony\Spy\SpyInterface;
use Eloquent\Phony\Stub\Answer\Builder\Factory\GeneratorAnswerBuilderFactory;
use Eloquent\Phony\Stub\Answer\Builder\Factory\GeneratorAnswerBuilderFactoryInterface;
use Eloquent\Phony\Stub\StubInterface;
use Eloquent\Phony\Stub\StubVerifier;

/**
 * Creates stub verifiers.
 */
class StubVerifierFactory implements StubVerifierFactoryInterface
{
    /**
     * Get the static instance of this factory.
     *
     * @return StubVerifierFactoryInterface The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(
                StubFactory::instance(),
                SpyFactory::instance(),
                MatcherFactory::instance(),
                MatcherVerifier::instance(),
                CallVerifierFactory::instance(),
                AssertionRecorder::instance(),
                AssertionRenderer::instance(),
                InvocableInspector::instance(),
                Invoker::instance(),
                GeneratorAnswerBuilderFactory::instance()
            );
        }

        return self::$instance;
    }

    /**
     * Construct a new stub verifier factory.
     *
     * @param StubFactoryInterface                   $stubFactory                   The stub factory to use.
     * @param SpyFactoryInterface                    $spyFactory                    The spy factory to use.
     * @param MatcherFactoryInterface                $matcherFactory                The matcher factory to use.
     * @param MatcherVerifierInterface               $matcherVerifier               The macther verifier to use.
     * @param CallVerifierFactoryInterface           $callVerifierFactory           The call verifier factory to use.
     * @param AssertionRecorderInterface             $assertionRecorder             The assertion recorder to use.
     * @param AssertionRendererInterface             $assertionRenderer             The assertion renderer to use.
     * @param InvocableInspectorInterface            $invocableInspector            The invocable inspector to use.
     * @param InvokerInterface                       $invoker                       The invoker to use.
     * @param GeneratorAnswerBuilderFactoryInterface $generatorAnswerBuilderFactory The generator answer builder factory to use.
     */
    public function __construct(
        StubFactoryInterface $stubFactory,
        SpyFactoryInterface $spyFactory,
        MatcherFactoryInterface $matcherFactory,
        MatcherVerifierInterface $matcherVerifier,
        CallVerifierFactoryInterface $callVerifierFactory,
        AssertionRecorderInterface $assertionRecorder,
        AssertionRendererInterface $assertionRenderer,
        InvocableInspectorInterface $invocableInspector,
        InvokerInterface $invoker,
        GeneratorAnswerBuilderFactoryInterface $generatorAnswerBuilderFactory
    ) {
        $this->stubFactory = $stubFactory;
        $this->spyFactory = $spyFactory;
        $this->matcherFactory = $matcherFactory;
        $this->matcherVerifier = $matcherVerifier;
        $this->callVerifierFactory = $callVerifierFactory;
        $this->assertionRecorder = $assertionRecorder;
        $this->assertionRenderer = $assertionRenderer;
        $this->invocableInspector = $invocableInspector;
        $this->invoker = $invoker;
        $this->generatorAnswerBuilderFactory = $generatorAnswerBuilderFactory;
    }

    /**
     * Create a new stub verifier.
     *
     * @param StubInterface|null $stub The stub, or null to create an anonymous stub.
     * @param SpyInterface|null  $spy  The spy, or null to spy on the supplied stub.
     *
     * @return StubVerifierInterface The newly created stub verifier.
     */
    public function create(StubInterface $stub = null, SpyInterface $spy = null)
    {
        if (!$stub) {
            $stub = $this->stubFactory->create();
        }
        if (!$spy) {
            $spy = $this->spyFactory->create($stub);
        }

        return new StubVerifier(
            $stub,
            $spy,
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            $this->invocableInspector,
            $this->invoker,
            $this->generatorAnswerBuilderFactory
        );
    }

    /**
     * Create a new stub verifier for the supplied callback.
     *
     * @param callable|null $callback The callback, or null to create an anonymous stub.
     *
     * @return StubVerifierInterface The newly created stub verifier.
     */
    public function createFromCallback($callback = null)
    {
        $stub = $this->stubFactory->create($callback);

        return new StubVerifier(
            $stub,
            $this->spyFactory->create($stub),
            $this->matcherFactory,
            $this->matcherVerifier,
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            $this->invocableInspector,
            $this->invoker,
            $this->generatorAnswerBuilderFactory
        );
    }

    private static $instance;
    private $stubFactory;
    private $spyFactory;
    private $matcherFactory;
    private $matcherVerifier;
    private $callVerifierFactory;
    private $assertionRecorder;
    private $assertionRenderer;
    private $invocableInspector;
    private $invoker;
    private $generatorAnswerBuilderFactory;
}
