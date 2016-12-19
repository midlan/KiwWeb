<?php

declare(strict_types=1);

use \Tracy\Debugger;

require_once '../vendor/autoload.php';

//debugger
Debugger::enable(Debugger::DEVELOPMENT, realpath('../log'));
error_reporting(-1);

//autoloader
$loader = new \Nette\Loaders\RobotLoader;
$loader->addDirectory('../classes');
$loader->setCacheStorage(new \Nette\Caching\Storages\FileStorage('../temp')); //kam kešovat autoloader
$loader->register();

//spuštění aplikace
$app = new \KivWeb\App;
$app->run('../config.json');
