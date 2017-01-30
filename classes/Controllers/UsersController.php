<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

use KivWeb\Models\User;

class UsersManageController extends BaseController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo list of users
    }
    
    public function deleteAction() {
        
        //todo delete user
        //redirect to indexAction
    }
    
    public function banAction() {
        
        //todo ban user
        //redirect to indexAction
    }
    
    public function editAction() {
        
        //todo edit of user
    }
    
    public function saveAction() {
        
        //todo edit validate of user
        //redirect to indexAction on succes
    }

    public function getRequiredRole(): int {
        return User::ROLE_ADMIN;
    }

}