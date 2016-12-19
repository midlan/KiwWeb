<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

abstract class BaseController {
    
    private $app;
    
    /**
     * 
     * @return \KivWeb\App
     */
    final protected function getApp() {
        return $this->app;
    }
    
    final public function init(\KivWeb\App $app) {
        $this->app = $app;
    }
    
    abstract public function getPermissionLevel();
    
}