<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

use \KivWeb\App,
    \KivWeb\Models\User;

class IndexController extends BaseController {
    
    public function indexAction() {
        
        $app = $this->getApp();
        $twig = $this->getApp()->getTwig();
        
        $posts = \KivWeb\Models\Post::getArrayApproved($app->getConnection());
        
        $template = $twig->load('index.twig');
        
        echo $template->render(array(
            'posts' => $posts,
        ));
    }
    
    public function loginValidateAction() {
        
        $app = $this->getApp();
        
        if(isset($_POST['username']) && isset($_POST['password'])) {
            
            $user = $app->getUser();
            
            //zkusit přihlásit
            if($user->loadByLogin((string)$_POST['username'], (string)$_POST['password'])) {
                $app->addMessage(App::MESSAGE_SUCCESS, 'Přihlášení bylo úspěšné.');
            }
            else {
                $app->addMessage(App::MESSAGE_ERROR, 'Nesprávné jméno nebo heslo.');
            }
            
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('index'), true, 302);
    }
    
    public function logoutAction() {
        
        $app = $this->getApp();
        
        $user = $app->getUser();

        $user->clear();

        $app->addMessage(App::MESSAGE_SUCCESS, 'Odhlášení bylo úspěšné.');
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('index'), true, 302);
    }
    
    public function registrationAction() {
        
        $app = $this->getApp();
        $user = $app->getUser();
        
        //odhlásit
        $user->clear();
        
        $twig = $app->getTwig();

        $template = $twig->load('registration.twig');
        
        echo $template->render();
    }
    
    public function registrationValidateAction() {
        
        $app = $this->getApp();
        
        if(
            isset($_POST['user_id'])
            && isset($_POST['username'])
            && isset($_POST['password'])
            && isset($_POST['password_twice'])
            && isset($_POST['name'])
            && isset($_POST['email'])
            && isset($_POST['organization'])
        ) {
            
            if($_POST['password_twice'] !== $_POST['password']) {
                $app->addMessage(App::MESSAGE_ERROR, 'Hesla se neshodují.');
                header('Location: ' . $app->getRouter()->buildUrl('index'), true, 302);
                return;
            }
        
            $newUser = new User($app);

            $newUser->fetchInto($_POST);

            //role
            $newUser->setRole(User::ROLE_AUTHOR);

            //uložení
            if($newUser->save()) {
                $app->addMessage(App::MESSAGE_SUCCESS, 'Registrace byla úspěná.');
                
                //přihlásit
                $user = $app->getUser();
                $user->clear();
                $user->setUserId($newUser->getUserId());
            }
            else {
                $app->addMessage(App::MESSAGE_ERROR, 'Registrace se nezdařila.');
            }
        }
        
        header('Location: ' . $app->getRouter()->buildUrl('index'), true, 302);
    }

    public function getRequiredRole(): int {
        return User::ROLE_NONE;
    }

}