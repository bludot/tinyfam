<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('VENDOR', ROOT . DS . 'vendor');



require_once (VENDOR . DS . 'autoload.php');
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


$app = require_once (ROOT . DS . 'Bootstrap.php');
$app->boot();
$app->init();
