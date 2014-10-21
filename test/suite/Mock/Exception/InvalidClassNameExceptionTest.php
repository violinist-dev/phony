<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class InvalidClassNameExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $className = '1';
        $cause = new Exception();
        $exception = new InvalidClassNameException($className, $cause);

        $this->assertSame($className, $exception->className());
        $this->assertSame("Invalid class name '1'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
