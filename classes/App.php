<?php

declare(strict_types=1);

namespace KivWeb;

use \Tracy\Debugger,
    \Tracy\ILogger as LogLevel;

class App {
    
    private $config;
    private $router;
    private $pdo;
    
    /**
     * 
     * @return Router
     */
    public function getRouter() {
        
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
    
    public function getPdo() {
        //todo
    }
    
    public function getUser() {
        
    }

    public function run(string $configFile) {
        
        //načtení konfigurace aplikace
        $configJson = file_get_contents($configFile);
        
        if($configJson === false) {
            $this->response500('Cannot read config file ' . $configFile);
            return;
        }
        
        $this->config = json_decode($configJson, true);
        
        if(!is_array($this->config)) {
            
            if($this->config === null) {
                $this->response500('Cannot parse config: ' . json_last_error_msg());
            }
            else {
                $this->response500('Config is not array.');
            }
            
            return;
        }
        
        //spustit kontroller
        $this->getRouter()->route($_GET, $this);
    }
    
    //todo 403
    
    public function response404(string $reason = '') {
        
        //log reason
        if($reason !== '') {
            Debugger::log("HTTP 404 reason: $reason", LogLevel::INFO);
            Debugger::fireLog("HTTP 404 reason: $reason");
        }
        
        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", true, 404);
        echo 404; //todo body
    }
    
    public function response500(string $reason = '') {
        
        //log reason
        if($reason !== '') {
            Debugger::log("HTTP 500 reason: $reason", LogLevel::CRITICAL);
            Debugger::fireLog("HTTP 500 reason: $reason");
        }
        
//        header("{$_SERVER['SERVER_PROTOCOL']} Internal Server Error", true, 500); //todo fixit, makes 502
        echo 500; //todo body
    }
}
