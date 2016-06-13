<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Eloquent\Asplode\Asplode;
use Eloquent\Phony\Pho\Phony;

Asplode::install();
error_reporting(-1);

describe('Phony', function () {
    beforeEach(function () {
        $this->handle = Phony::mock('Eloquent\Phony\Test\TestClassA');
        $this->mock = $this->handle->mock();
    });

    it('should record passing mock assertions', function () {
        $this->mock->testClassAMethodA('a', 'b');

        $this->handle->testClassAMethodA->calledWith('a', 'b');
    });

    it('should record failing mock assertions', function () {
        $this->handle->testClassAMethodA->calledWith('a', 'b');
    });
});