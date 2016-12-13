<?php

declare(strict_types=1);

namespace KivWeb;

use \Tracy\Debugger,
    \Tracy\ILogger as LogLevel;

class App {
    
    private $router;


    public function run() {
        
        $route = array_key_exists(self::ACTION_KEY, $_GET) && is_string($_GET[self::ACTION_KEY]) ? $_GET[self::ACTION_KEY] : '';
        
        //todo services initialize
        
        $this->route($route);
    }
    
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
    
    public function getUrl(string $controller, string $method, array $query = []) {
        
        return ;
        
        //todo
    }
}
