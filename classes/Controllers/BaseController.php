<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

abstract class BaseController {
    
    private $app;
    
    final protected function getApp(): \KivWeb\App {
        return $this->app;
    }
    
    final public function init(\KivWeb\App $app) {
        $this->app = $app;
    }
    
    abstract public function getRequiredRole(): int;
    
}