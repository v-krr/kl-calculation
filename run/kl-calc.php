<?php
declare(strict_types=1);

$root = realpath(dirname(__FILE__) . '/../');
require $root . '/vendor/autoload.php';

vkrr\kblayout\calc\console\Console::run();

