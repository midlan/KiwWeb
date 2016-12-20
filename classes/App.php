<?php

declare(strict_types=1);

namespace KivWeb;

use \Tracy\Debugger,
    \Tracy\ILogger as LogLevel;

class App {
    
    private $config;
    
    //services
    private $router = null;
    private $twig = null;
    private $pdo = null;
    
    public function getRouter(): \KivWeb\Router {
        
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
        }
        
        return $this->twig;
    }
    
    public function getPDO(): \PDO {
        
        $conf = $this->config['PDO'];
        
        new PDO($conf['dsn'], $conf['username'], $conf['password'], []);
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
