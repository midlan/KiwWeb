<?php

use \Tracy\Debugger;

require_once '../vendor/autoload.php';

//debugger
Debugger::enable(Debugger::DEVELOPMENT);

//autoloader
$loader = new \Nette\Loaders\RobotLoader;
$loader->addDirectory('../app');
$loader->setCacheStorage(new \Nette\Caching\Storages\FileStorage('../temp')); //kam kešovat autoloader
$loader->register();

//spuštění aplikace
$app = new \KivWeb\App;
$app->run();
