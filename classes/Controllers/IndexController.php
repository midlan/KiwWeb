<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class IndexController extends BaseController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        $template = $twig->load('skeleton.twig');
        
        echo $template->render(array('a_variable' => 'Hello world!'));
        
        //todo show approved posts
    }
    
    public function loginValidateAction() {
        
        $app = $this->getApp();
        
        if(isset($_POST['username']) && isset($_POST['password'])) {
            
            $user = $app->getUser();
            
            if($user->loadByLogin((string)$_POST['username'], (string)$_POST['password'])) {
                //todo add to message
                'Přihlášení bylo úspěšné';
            }
            else {
                //todo add to message
                'Nesprávné jméno nebo heslo';
            }
            
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('index', 'index'), true, 302);
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
        return \KivWeb\Models\User::ROLE_NONE;
    }

}