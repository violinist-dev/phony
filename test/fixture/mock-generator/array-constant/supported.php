<?php

$message = 'Requires constant expression support.';

return !defined('HHVM_VERSION') &
    version_compare(PHP_VERSION, '5.6.0-dev', '>=');
