<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class PostsAuthoringController extends PostsController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo list of my posts with reviews mark
    }
    
    public function addAction() {
        
        //todo formulář na přidání postu
    }
    
    public function editAction() {
        
        //todo edit of post
    }
    
    public function saveAction() {
        
        //save new or edited post
    }
    
    public function deleteAction() {
        
        //todo delete
    }
    

    public function getRequiredRole(): int {
        return \KivWeb\Models\User::ROLE_AUTHOR;
    }

}