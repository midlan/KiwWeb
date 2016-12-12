<?php

namespace KivWeb;

class App {
    
    const ACTION_KEY = 'p';
    const ACTION_DELIMITER = '/';
    const DEFAULT_CONTROLLER = 'Index';
    const DEFAULT_METHOD = 'index';
    const METHOD_SUFFIX = 'Action';
    
    public function run() {
        
        $action = array_key_exists(self::ACTION_KEY, $_GET) ? $_GET[self::ACTION_KEY] : '';
        
        //todo databáze
        
        $this->route($action);
    }
    
    private function route($action) {
        
        $parts = explode(self::ACTION_DELIMITER, $action, 2);
        
        $name = self::DEFAULT_CONTROLLER;
        $method = self::DEFAULT_METHOD . self::METHOD_SUFFIX;
        
        if($parts[0] !== '') {
            
            $name = $parts[0];
            $name[0] = \chr(\ord($name[0]) - 32); //první písmeno vždy velké, todo co když to bude utf8?
            
            if(array_key_exists(1, $parts) && $parts[1] !== '') {
                $method = $parts[1] . self::METHOD_SUFFIX;
            }
        }
        
        $nsName = '\\' . __NAMESPACE__ . '\\Controllers\\' . $name;
        
        var_dump($nsName, class_exists($nsName));
        
        //kontroler existuje
        if(class_exists($nsName)) {
            
            $controller = new $nsName;
            
            //metodu lze zavolat
            if(is_callable(array($controller, $method))) {
                $controller->{$method}();
                return;
            }
        }
        
        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", true, 404);
        echo 404; //todo
    }
    
    public function getUrl($action) {
        //todo
    }
}
