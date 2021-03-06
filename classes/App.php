<?php

declare(strict_types=1);

namespace KivWeb;

use \Tracy\Debugger,
    \Tracy\ILogger as LogLevel;

class App {
    
    const MESSAGE_SUCCESS = 'success';
    const MESSAGE_INFO = 'info';
    const MESSAGE_WARNING = 'warning';
    const MESSAGE_ERROR = 'danger';
    
    private $config;
    
    //services
    private $router = null;
    private $twig = null;
    private $conn = null;
    private $user = null;
    
    public function getRouter(): Router {
        
        if($this->router === null) {
            
            $conf = $this->config['router'];
            
            $this->router = new Router(
                $conf['route_key'],
                $conf['route_delimiter'],
                $conf['default_section'],
                $conf['default_action'],
                $conf['controller_namespace'],
                $conf['controller_parent']
            );
        }
        
        return $this->router;
    }
    
    public function getTwig(): \Twig_Environment {
        
        if($this->twig === null) {
            
            $conf = $this->config['twig'];
            
            $loader = new \Twig_Loader_Filesystem($conf['templates_dir']);
            $this->twig = new \Twig_Environment($loader, $conf['environment']);
            
            //definice globálních proměnných
            $this->twig->addGlobal('app', $this);
        }
        
        return $this->twig;
    }
    
    public function getConnection(): \PDO {
        
        if($this->conn === null) {
            
            //todo koncipovat konfig obecně ne přímo pro PDO?
            $conf = $this->config['pdo'];
        
            $this->conn = new \PDO($conf['dsn'], $conf['username'], $conf['password'], []);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        
        return $this->conn;
    }
    
    private function saveSessionUserId() {
        
        //uživatel nebyl použit => není co ukládat
        if($this->user === null) {
            return;
        }
        
        $_SESSION['user_id'] = $this->user->getUserId();
    }
    
    public function getUser(): Models\User {
        
        if($this->user === null) {
            
            $this->user = new Models\User($this);
            
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            //načíst přihlášeného
            if(array_key_exists('user_id', $_SESSION) && is_int($_SESSION['user_id'])) {
                $this->user->loadById($_SESSION['user_id']);
            }
            
            //zablokovaný účet
            if($this->user->getBannedDate() !== null) {
                $this->user->clear();
                $this->addMessage(App::MESSAGE_ERROR, 'Zablokovaný uživatelský účet');
            }
        }
        
        return $this->user;
    }
    
    public function addMessage(string $type, string $message) {
        
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $messageItem = array(
            'type' => $type,
            'text' => $message,
        );
        
        //pole zpráv v session už existuje, přidat zprávu
        if(array_key_exists('messages', $_SESSION) && is_array($_SESSION['messages'])) {
            $_SESSION['messages'][] = $messageItem;
        }
        //založit pole
        else {
            $_SESSION['messages'] = array($messageItem);
        }
    }
    
    public function getCleanMessages(): array {
        
        if(session_status() === PHP_SESSION_NONE) {
            return array();
        }
        
        if(array_key_exists('messages', $_SESSION) && is_array($_SESSION['messages'])) {
            $messages = $_SESSION['messages'];
            $_SESSION['messages'] = array(); //clean
            return $messages;
        }
        
        return array();
    }

    public function run(string $configFile) {
        
        //načtení konfigurace aplikace
        $configJson = file_get_contents($configFile);
        
        if($configJson === false) {
            $this->errorResponse(500, 'Cannot read config file ' . $configFile);
            return;
        }
        
        $this->config = json_decode($configJson, true);
        
        if(!is_array($this->config)) {
            
            if($this->config === null) {
                $this->errorResponse(500, 'Cannot parse config: ' . json_last_error_msg());
            }
            else {
                $this->errorResponse(500, 'Config is not array.');
            }
            
            return;
        }
        
        //spustit kontroller
        $this->getRouter()->route($_GET, $this);
        
        //aktualizovat uživatele
        $this->saveSessionUserId();
    }
    
    public function errorResponse(int $code, string $reason = '') {
        
        //log reason
        if($reason !== '') {
            Debugger::log("HTTP $code reason: $reason", LogLevel::INFO);
            Debugger::fireLog("HTTP $code reason: $reason");
        }
        
        //todo error 500 házel 502, zkontrolovat
        http_response_code($code);
        echo $code;
    }
    
}
