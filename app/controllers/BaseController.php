<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

abstract class BaseController {
    
    final public function init(\KivWeb\App $app) {
        //todo get db, user, session, template, etc
    }
    
    abstract public function getPermissionLevel();
    
}