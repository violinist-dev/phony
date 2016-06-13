<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../vendor/simpletest/simpletest/autorun.php';

use Eloquent\Asplode\Asplode;
use Eloquent\Phony\Simpletest\Phony;

Asplode::install();
error_reporting(-1);

class PhonyTest extends UnitTestCase
{
    public function setUp()
    {
        $this->handle = Phony::mock('Eloquent\Phony\Test\TestClassA');
        $this->mock = $this->handle->mock();
    }

    public function testShouldRecordPassingMockAssertions()
    {
        $this->mock->testClassAMethodA('a', 'b');

        $this->handle->testClassAMethodA->calledWith(new EqualExpectation('a'), 'b');
    }

    public function testShouldRecordFailingMockAssertions()
    {
        $this->handle->testClassAMethodA->calledWith('a', 'b');
    }
}