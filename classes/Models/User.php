<?php

declare(strict_types=1);

namespace KivWeb\Models;


class User extends BaseModel {
    
    const ROLE_NONE = 0b000;
    const ROLE_AUTHOR = 0b001;
    const ROLE_REVIEWER = 0b010;
    const ROLE_ADMIN = 0b100;
    
    private $userId ;
    private $role ;
    private $username ;
    private $passwordHash ;
    private $name ;
    private $email ;
    private $organization ;
    private $bannedDate ;

    public function getUserId() {
        return $this->userId;
    }

    public function getRole() {
        return $this->role;
    }

    public function getUsername() {
        return $this->username;
    }
    
    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getOrganization() {
        return $this->organization;
    }

    public function getBannedDate()  {
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
    
    public function setPassword(string $password) {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
    
    protected function setPasswordHash(string $password) {
        $this->passwordHash = $password;
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
        if($bannedDate === '') {
            $this->bannedDate = null;
        }
        else {
            $this->bannedDate = $bannedDate;;
        }
    }

    public function clearBannedDate() {
        $this->bannedDate = null;
    }
    
    public function clear() {
        $this->userId = null;
        $this->role = null;
        $this->username = null;
        $this->passwordHash = null;
        $this->name = null;
        $this->email = null;
        $this->organization = null;
        $this->bannedDate = null;
    }
    
    public function isAllowedTo(int $role): bool {
        
        $thisRole = $this->getRole();
        
        if($thisRole === null) {
            return false; 
        }
        
        return $thisRole & $role === $role;
    }
    
    public function fetchInto(array $data) {
        
        $this->clear();
        
        $this->setUserId((int)$data['user_id']);
        $this->setRole((int)$data['role']);
        $this->setUsername($data['username']);
        $this->setPasswordHash($data['password_hash']);
        $this->setName($data['name']);
        $this->setEmail($data['email']);
        $this->setOrganization($data['organization']);

        if($data['banned_date'] !== null) {
            $this->setBannedDate($data['banned_date']);
        }
    }
    
    public function loadById(int $userId): bool {
        
        $this->clear();
        
        $stmt =  $this->getConnection()->prepare('SELECT * FROM users WHERE user_id = :user_id LIMIT 1;');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //uřivatel nalzen
        if($data !== false) {
            $this->fetchInto($data);
            return true;
        }
        
        return false;
    }
    
    public function loadByLogin(string $username, string $password): bool {
        
        $this->clear();
        
        $stmt = $this->getConnection()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1;');
        
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //uřivatel nalzen a hesla se shodují
        if($data !== false && password_verify($password, $data['password_hash'])) {
            $this->fetchInto($data);
            return true;
        }
        
        return false;
    }
    
    public function save(): bool {
        
        $new = $this->getUserId() === null;
        
        $query = $new ? 'INSERT INTO' : 'UPDATE';
        
        $query .= ' users SET '
            . 'user_id = :user_id,'
            . 'role = :role,'
            . 'username = :username,'
            . 'password_hash = :password_hash,'
            . 'name = :name,'
            . 'email = :email,'
            . 'organization = :organization,'
            . 'banned_date = :banned_date';

        if(!$new) {
            $query .= ' WHERE user_id = :user_id';
        }
        
        $query .= ';';
        
        $conn = $this->getConnection();
        
        $stmt = $conn->prepare($query);
        
        $success = $stmt->execute(array(
            ':user_id' => $this->getUserId(),
            ':role' => $this->getRole(),
            ':username' => $this->getUsername(),
            ':password_hash' => $this->getPasswordHash(),
            ':name' => $this->getName(),
            ':email' => $this->getEmail(),
            ':organization' => $this->getOrganization(),
            ':banned_date' => $this->getBannedDate(),
        ));
        
        if($success && $new) {
            $this->setUserId((int)$conn->lastInsertId());
        }
        
        return $success;
    }
    
    public static function getArrayByRole(\PDO $conn, int $role): array {
        
        $stmt = $conn->prepare('SELECT * FROM users WHERE role & :role = :role;');
        
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $data === false ? array() : $data;
    }
}
