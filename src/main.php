<?php

const DS = DIRECTORY_SEPARATOR;
const ROOT = __DIR__;
const APP_ROOT = ROOT . DS . 'app';
const CONFIG_DIR = APP_ROOT . DS . 'config';

require_once APP_ROOT . DS . 'bootstrap.php';

use MagedIn\Lab\ObjectManager;
use MagedIn\Lab\App;

/** @var App $app */
$app = ObjectManager::getInstance()->create(App::class);
$app->run();
