<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Proxy;

use Eloquent\Phony\Mock\MockInterface;
use Eloquent\Phony\Stub\StubVerifierInterface;
use ReflectionProperty;

/**
 * A proxy for controlling a mock.
 *
 * @internal
 */
class MockProxy extends AbstractMockProxy implements InstanceMockProxyInterface
{
    /**
     * Construct a new mock proxy.
     *
     * @param MockInterface                            $mock  The mock.
     * @param array<string,StubVerifierInterface>|null $stubs The stubs.
     */
    public function __construct(MockInterface $mock, array $stubs = null)
    {
        if (null === $stubs) {
            $stubsProperty = new ReflectionProperty($mock, '_stubs');
            $stubsProperty->setAccessible(true);
            $stubs = $stubsProperty->getValue($mock);
        }

        parent::__construct(get_class($mock), $stubs);

        $this->mock = $mock;
    }

    /**
     * Get the mock.
     *
     * @return MockInterface The mock.
     */
    public function mock()
    {
        return $this->mock;
    }

    private $mock;
}