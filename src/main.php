<?php

const APP_ROOT = __DIR__;

require_once __DIR__ . '/app/bootstrap.php';

use MagedIn\Lab\ObjectManager;
use MagedIn\Lab\App;

/** @var App $app */
$app = ObjectManager::getInstance()->create(App::class);
$app->run();
