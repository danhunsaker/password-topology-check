<?php

namespace DanHunsaker\PasswordTopology\Tests;

use DanHunsaker\PasswordTopology\Topology;

require_once('vendor/autoload.php');

$testValues = [
    'failme18!' => ['topology' => 'lllllldds', 'check' => false],
    'PaSs1337?' => ['topology' => 'ululdddds', 'check' => true],
    'PaSs?1337' => ['topology' => 'ululsdddd', 'check' => true],
    '?PaSs1337' => ['topology' => 'sululdddd', 'check' => true],
    // Test with a # in various positions
    'failme18#' => ['topology' => 'lllllldds', 'check' => false],
    'PaSs1337#' => ['topology' => 'ululdddds', 'check' => true],
    'PaSs#1337' => ['topology' => 'ululsdddd', 'check' => true],
    '#PaSs1337' => ['topology' => 'sululdddd', 'check' => true],
];

$testMap = [
    false => "forbidden",
    true  => "allowed",
];

$pass = true;

foreach ($testValues as $test => $expect) {
    if (runTests($test, $expect)) {
        echo "Test PASS: {$test} :: {$expect['topology']} ({$testMap[$expect['check']]})\n";
    } else {
        echo "Test FAIL: {$test} :: {$expect['topology']} ({$testMap[$expect['check']]})\n";
        $pass = false;
    }
}

if (!$pass) {
    exit(1);
}

function runTests($test, $expect)
{
    $success = true;

    if (Topology::convert($test) !== $expect['topology']) {
        echo "Topology mismatch!\n";
        $success = false;
    }

    if (Topology::check($test) !== $expect['check']) {
        echo "Normal check fail!\n";
        $success = false;
    }

    Topology::allow($expect['topology']);
    if (Topology::check($test) !== true) {
        echo "Allow check fail!\n";
        $success = false;
    }

    Topology::forbid($expect['topology']);
    if (Topology::check($test) !== false) {
        echo "Forbid check fail!\n";
        $success = false;
    }

    if ($expect['check']) {
        Topology::allow($expect['topology']);
    }

    return $success;
}
