<?php

declare(strict_types=1);

namespace KivWeb;

class Router {
    
    private $routeKey;
    private $routeDelimiter;
    private $defaultSection;
    private $defaultAction;
    private $controllersNamespace;
    private $controllersParent;

    public function __construct($routeKey, $routeDelimiter, $defaultSection, $defaultAction, $controllersNamespace, $controllersParent) {
        $this->routeKey = $routeKey;
        $this->routeDelimiter = $routeDelimiter;
        $this->defaultSection = $defaultSection;
        $this->defaultAction = $defaultAction;
        $this->controllersNamespace = $controllersNamespace;
        $this->controllersParent = $controllersParent;
    }
    
    public function buildUrl(string $section = '', string $action = '', array $query = []): string {
        
        $path = $this->buildPath($section, $action, $query);
        
        $pathAbs = preg_replace('~^\\./~', '', $path); //cut ./ from beggining
        
        return "http://{$_SERVER['HTTP_HOST']}/{$pathAbs}";
    }
    
    public function buildPath(string $section = '', string $action = '', array $query = []): string {
        
        $route = $this->buildRoute($section, $action);
        
        if($route !== '') {
            $query = [$this->routeKey => $route] + $query;
        }
        
        $urlPath = './';
        
        if($query) {
            $urlPath .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }
        
        return $urlPath;
    }
    
    public function buildRoute(string $section = '', string $action = ''): string {
        
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
    
    public function parseRoute(string $route): array {
        
        $parts = explode($this->routeDelimiter, $route, 2);
        
        $section = $parts[0] === '' ? $this->defaultSection : $parts[0];
        $action = array_key_exists(1, $parts) ? $parts[1] : $this->defaultAction;
        
        if($action === '') {
            $action = $this->defaultAction;
        }
        
        return [$section, $action];
    }
    
    public function getControllerClass(string $section = ''): string {
        
        if($section === '') {
            $section = $this->defaultSection;
        }
        
        $section[0] = \chr(\ord($section[0]) - 32); //zvětšit první písmeno, jen pokud bylo malé; todo co utf8?
        
        return "{$this->controllersNamespace}\\{$section}Controller";
    }
    
    public function getControllerMethod(string $action = ''): string {
        
        if($action === '') {
            $action = $this->defaultAction;
        }
        
        return $action . 'Action';
    }
    
    public function getRoute(array $requestQuery): string {
        return array_key_exists($this->routeKey, $requestQuery) && is_string($requestQuery[$this->routeKey]) ? $requestQuery[$this->routeKey] : '';
    }
    
    public function route(array $requestQuery, App $app) {
        
        $route = $this->getRoute($requestQuery);
        
        list($section, $action) = $this->parseRoute($route);
        
        //záskání názvu třídy a metody
        $class = $this->getControllerClass($section);
        $method = $this->getControllerMethod($action);
        
        //kontrola existence třídy kontroleru
        if(!class_exists($class)) {
            $app->errorResponse(404, "Class $class not found");
            return;
        }
        
        //instance
        /* @var $controller \KivWeb\Controllers\BaseController */
        $controller = new $class;
        
        //kontrola platnosti kontroleru
        $parentClass = $this->controllersNamespace . '\\' . $this->controllersParent;
        
        if(!($controller instanceof $parentClass)) {
            $app->errorResponse(500, "$class is not child of $parentClass");
            return;
        }
            
        //kontrola cílové metody
        if(!is_callable([$controller, $method])) {
            $app->errorResponse(404, "Method $class::$method is not callable");
            return;
        }
        
        //kontrola práv uživatele
        if(!$app->getUser()->isAllowedTo($controller->getRequiredRole())) {
            //todo 403
        }
        
        //inicializace a zavolání kontroleru
        $controller->init($app);
        $controller->{$method}();
    }
    
    
}
