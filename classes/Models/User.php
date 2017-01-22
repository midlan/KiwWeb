<?php

declare(strict_types=1);

namespace KivWeb\Models;


class User {
    
    private $userId = null;
    private $role = null;
    private $username = null;
    private $name = null;
    private $email = null;
    private $organization = null;
    private $bannedDate = null;
    
    public function getUserId(): int {
        return $this->userId;
    }

    public function getRole(): int {
        return $this->role;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getOrganization(): string {
        return $this->organization;
    }

    public function getBannedDate(): string {
        return $this->bannedDate;
    }

    public function setUserId(int $userId) {
        $this->userId = $userId;
    }

    public function setRole(int $role) {
        $this->role = $role;
    }

    public function setUsername(string $username) {
        $this->username = $username;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function setEmail(string $email) {
        $this->email = $email;
    }

    public function setOrganization(string $organization) {
        $this->organization = $organization;
    }

    public function setBannedDate(string $bannedDate) {
        $this->bannedDate = $bannedDate;
    }
    
    public function loadById(\PDO $pdo, int $userId): bool {
        //todo load
    }
    
    public function loadByLogin(\PDO $pdo, string $username, string $password): bool {
        //todo load
    }
    
    public function isAllowedTo(int $role): bool {
        
        if($this->role === null) {
            return false; 
        }
        
        return $this->role & $role === $role;
    }
    
    public function save(\PDO $pdo): bool {
        
        if($this->userId === null) {
            //todo insert
        }
        else {
            //todo update
        }
    }
    
    static function getArray(int $role): array {
        
    }
}
