<?php

declare(strict_types=1);

namespace KivWeb\Models;


abstract class BaseModel {
    
    private $app;
    
    public function __construct(\KivWeb\App $app) {
        $this->clear();
        $this->app = $app;
    }
    
    public function getConnection(): \PDO {
        return $this->app->getConnection();
    }

    abstract public function clear();
    
    abstract public function fetchInto();
}
