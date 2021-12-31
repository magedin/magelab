<?php

use MagedIn\Lab\Config;

require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

Config::load();

if (Config::get('mode') == 'develop') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

if (version_compare(PHP_VERSION, '7.3.0', '<')) {
    throw new \ErrorException("PHP Version is lower than 7.3.0. Please upgrade your runtime.");
}
