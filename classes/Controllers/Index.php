<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class Index extends BaseController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        $template = $twig->load('skeleton.twig');
        
        echo $template->render(array('a_variable' => 'Hello world!'));
        
        //todo show approved posts
    }
    
    public function loginValidateAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo co s přihlášeným hňupem?
        //todo user & password login
    }
    
    public function registrationAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo co s přihlášeným hňupem?
        //todo show register form
    }
    
    public function registrationValidateAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo validate form
        //create new user
        //new user is author
    }

    public function getRequiredRole(): int {
        return \KivWeb\Role::NONE;
    }

}