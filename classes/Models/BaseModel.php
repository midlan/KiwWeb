<?php

declare(strict_types=1);

namespace KivWeb\Models;


abstract class BaseModel {
    
    private $conn;
    
    public function __construct(\PDO $conn) {
        $this->clear();
        $this->conn = $conn;
    }
    
    public function getConnection(): \PDO {
        return $this->conn;
    }

    abstract public function clear();
}
