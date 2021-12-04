<?php

const APP_ROOT = __DIR__;

require_once __DIR__ . '/app/bootstrap.php';

$app = new \MageLab\App();
$app->run();
