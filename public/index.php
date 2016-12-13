<?php

declare(strict_types=1);

use \Tracy\Debugger;

require_once '../vendor/autoload.php';

//debugger
Debugger::enable(Debugger::DEVELOPMENT, realpath('../log'));
error_reporting(-1);

//autoloader
$loader = new \Nette\Loaders\RobotLoader;
$loader->addDirectory('../app/App.php');
$loader->addDirectory('../app/controllers');
$loader->setCacheStorage(new \Nette\Caching\Storages\FileStorage('../temp')); //kam kešovat autoloader
$loader->register();

//spuštění aplikace
$app = new \KivWeb\App;
$app->run();
