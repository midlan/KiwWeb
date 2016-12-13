<?php

declare(strict_types=1);

namespace KivWeb;

use \Tracy\Debugger,
    \Tracy\ILogger as LogLevel;

class Router {
    
    const ROUTE_KEY = 'p';
    const ACTION_DELIMITER = '/';
    const DEFAULT_CONTROLLER = 'Index';
    const DEFAULT_METHOD = 'index';
    const METHOD_SUFFIX = 'Action';
    
    private $routeKey;
    private $routeDelimiter;
    private $defaultSection;
    private $defaultAction;
    private $controllersNamespace;


    public function __construct() {
        
    }
    
    public function buildUrlPath(string $section, string $action, array $query = []) {
        
        $route = $this->buildRoute($section, $action);
        
        if($route !== '') {
            $query = [$this->routeKey => $route] + $query;
        }
        
        if($query) {
            return '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }
        
        return '';
    }
    
    public function buildRoute(string $section = '', string $action = '') {
        
        if($section === '' && $action !== '') {
            throw new Exception('Cannot build route with action and no section.');
        }
        
        //defaultní akci neuvádět
        if($action === $this->defaultAction) {
            $action = '';
        }
        
        //akce není uvedena (nebo byla výchozí)
        if($action === '') {
            
            //sekce je také výchozí (nebo neuvedená), neuvádět tedy nic
            if($section === '' || $section === $this->defaultSection) {
                return '';
            }
            
            //akce není, uvést jen sekci
            return $section;
        }
        
        //uvést obojí
        return $section . $this->routeDelimiter . $action;
    }
    
    public function parseRoute(string $route) {
        
        $parts = explode($this->routeDelimiter, $route, 2);
        
        $section = $parts[0] === '' ? $this->defaultSection : $parts[0];
        $action = array_key_exists(1, $parts) ? $parts[1] : $this->defaultAction;
        
        if($action === '') {
            $action = $this->defaultAction;
        }
        
        return [$section, $action];
    }
    
    public function getControllerClass(string $section = '', string $namespace = '') {
        
        if($section === '') {
            $section = $this->defaultSection;
        }
        
        $sction[0] = \chr(\ord($sction[0]) - 32); //zvětšit první písmeno, jen pokud bylo malé; todo co utf8?
        
        return "$namespace\\$section";
    }
    
    public function getControllerMethod(string $action = '') {
        
        if($action === '') {
            $action = $this->defaultAction;
        }
        
        return $action . 'Action';
    }
    
    public function getRoute(array $requestQuery) {
        return array_key_exists(self::ROUTE_KEY, $requestQuery) && is_string($requestQuery[self::ROUTE_KEY]) ? $requestQuery[self::ROUTE_KEY] : '';
    }
    
    //app zde slouží mimo jiné místo response (kódy 404 a 500)
    public function route(array $requestQuery, App $app) {
        
        $route = $this->getRoute($requestQuery);
        
        list($section, $action) = $this->parseRoute($route);
        
        //todo namesapce
        
        $class = $this->getControllerClass($section, $namespace);
        $method = $this->getControllerMethod($action);
        
        //kontrola existence třídy kontroleru
        if(!class_exists($class)) {
            $this->response404("Class $class not found");
            return;
        }
        
        $controller = new $class;
        
        //kontrola platnosti kontroleru
        if(!($controller instanceof Controllers\BaseController)) {
            Debugger::log("404 reason: $class is not child of BaseController");
            //todo 500
            return;
        }
            
        //kontrola cílové metody
        if(!is_callable([$controller, $method])) {
            $this->response404("Method $class::$method is not callable");
            return;
        }
        
        //todo práva
        
        //inicializace a zavolání
        $controller->init($app);
        $controller->{$method}();
    }
    
    
}
