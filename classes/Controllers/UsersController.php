<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

use \KivWeb\Models\User,
    \KivWeb\App;

class UsersManageController extends BaseController {
    
    public function indexAction() {
        
        $app = $this->getApp();
        $twig = $app->getTwig();
        
        $users = User::getArrayByRole($app->getConnection(), User::ROLE_NONE);
        
        $template = $twig->load('users.twig');
        
        echo $template->render(array(
            'users' => $users,
        ));
    }
    
    public function deleteAction() {
        
        $app = $this->getApp();
        
        if(isset($_POST['user_id'])) {
            
            $managedUser = new User($app);
            $managedUser->loadById((int)$_POST['user_id']);
            
            if($managedUser->isLoaded() && $managedUser->delete()) {
                $app->addMessage(App::MESSAGE_SUCCESS, 'Smazání uživatele bylo úspěšné.');
            }
            else {
                $app->addMessage(App::MESSAGE_ERROR,'Smazání uživatele se nezdařilo.');
            }
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
    }
    
    public function banAction() {
        
        $app = $this->getApp();
        
        if(isset($_POST['user_id'])) {
            
            $managedUser = new User($app);
            $managedUser->loadById((int)$_POST['user_id']);
            $managedUser->setBannedDate(date('Y-m-d H:i:s'));
            
            if($managedUser->isLoaded() && $managedUser->save()) {
                $app->addMessage(App::MESSAGE_SUCCESS, 'Uživatel byl zablokovaán.');
            }
            else {
                $app->addMessage(App::MESSAGE_ERROR, 'Zablokování uživatele se nepodařilo.');
            }
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
    }
    
    public function editAction() {
        
        $app = $this->getApp();
        
        if(!isset($_GET['user_id'])) {
            header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
            return;
        }
        
        $managedUser = new User($app);
        $managedUser->loadById((int)$_GET['user_id']);
        
        if(!$managedUser->isLoaded()) {
            $app->addMessage(App::MESSAGE_ERROR, 'Uživatel, kterého se pokoušíte upravit neexistuje.');
            header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
            return;
        }
        
        $twig = $app->getTwig();
        
        $template = $twig->load('user_edit.twig');
        
        echo $template->render(array(
            'managedUser' => $managedUser,
        ));
    }
    
    public function saveAction() {
        
        if(
            isset($_POST['user_id'])
            && isset($_POST['username'])
            && isset($_POST['name'])
            && isset($_POST['email'])
            && isset($_POST['organization'])
        ) {
        
            $managedUser = new User($app);
            $managedUser->loadById((int)$_POST['user_id']);

            if(!$managedUser->isLoaded()) {
                $app->addMessage(App::MESSAGE_ERROR, 'Uživatel kterého se pokoušíte upravit neexistuje.');
                header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
                return;
            }
            
            $managedUser->fetchInto($_POST);

            //heslo
            if(!empty($_POST['new_password'])) {
                $managedUser->setPassword((string)$_POST['new_password']);
            }

            //role
            $role = User::ROLE_NONE;

            if(isset($_POST['role_author'])) {
                $role |= User::ROLE_AUTHOR;
            }

            if(isset($_POST['role_reviewer'])) {
                $role |= User::ROLE_REVIEWER;
            }

            if(isset($_POST['role_admin'])) {
                $role |= User::ROLE_ADMIN;
            }

            $managedUser->setRole($role);

            //uložení
            if($managedUser->save()) {
                $app->addMessage(App::MESSAGE_SUCCESS, 'Změny uživatele byly uloženy.');
            }
            else {
                $app->addMessage(App::MESSAGE_ERROR, 'Uložení změn uživatele se nezdařilo.');
            }
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('usersManage'), true, 302);
    }

    public function getRequiredRole(): int {
        return User::ROLE_ADMIN;
    }

}