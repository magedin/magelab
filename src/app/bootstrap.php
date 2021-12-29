<?php

use MagedIn\Lab\Config;

require_once APP_ROOT . '/vendor/autoload.php';

Config::load();

if (Config::get('mode') == 'develop') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
