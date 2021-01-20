<?php

use MirazMac\PurePhpConfig\PurePhpConfig;

require_once '../vendor/autoload.php';

$config = new PurePhpConfig(__DIR__ . '/config');

// Access a value
var_dump($config->get('app.url'));


// Delete a key
var_dump($config->delete('app.twig.auto'));

echo "<br>";

// Access a nested value using dot notation
var_dump($config->get('app.twig.cache'));

echo "<br>";

// Check if a key exists
var_dump($config->exists('app.twig'));

echo "<br>";

// Provided with only namespace, so will return all data as array
var_dump($config->get('app'));
echo "<br>";

// Set a value
var_dump($config->set('app.url', 'https://google.com'));

echo "<br>";
// Set a nested value using dot notation
var_dump($config->set('app.twig.cache', false));

echo "<br>";
// Replace an entire namespace data
var_dump($config->set('app', ['name' => 'NewApp']));
