<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Spy;

use Eloquent\Phony\Call\Call;
use Eloquent\Phony\Call\Event\CallEventFactory;
use InvalidArgumentException;
use Traversable;

/**
 * Creates traversable spies.
 */
class TraversableSpyFactory
{
    /**
     * Get the static instance of this factory.
     *
     * @return TraversableSpyFactory The static factory.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self(CallEventFactory::instance());
        }

        return self::$instance;
    }

    /**
     * Construct a new traversable spy factory.
     *
     * @param CallEventFactory $callEventFactory The call event factory to use.
     */
    public function __construct(CallEventFactory $callEventFactory)
    {
        $this->callEventFactory = $callEventFactory;
    }

    /**
     * Create a new traversable spy.
     *
     * @param Call              $call        The call from which the traversable originated.
     * @param Traversable|array $traversable The traversable.
     *
     * @return Traversable              The newly created traversable spy.
     * @throws InvalidArgumentException If the supplied traversable is invalid.
     */
    public function create(Call $call, $traversable)
    {
        if ($traversable instanceof Traversable) {
            return new TraversableSpy(
                $call,
                $traversable,
                $this->callEventFactory
            );
        }

        if (is_array($traversable)) {
            return new ArraySpy($call, $traversable, $this->callEventFactory);
        }

        if (is_object($traversable)) {
            $type = var_export(get_class($traversable), true);
        } else {
            $type = gettype($traversable);
        }

        throw new InvalidArgumentException(
            sprintf('Unsupported traversable of type %s.', $type)
        );
    }

    private static $instance;
    private $callEventFactory;
}
