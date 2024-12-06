<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

$config = new Configuration();

$config
    ->addPathToScan(__DIR__ . '/../bin/aoc', isDev: false)
    ->addPathToScan(__DIR__ . '/../src', isDev: false)
    ->addPathToScan(__DIR__ . '/../tests', isDev: true)
;

if (version_compare(PHP_VERSION, '8.2.0') < 0) {
    //~ Whitelist
    $config->ignoreUnknownFunctions(['curl_upkeep']);
}

return $config;
