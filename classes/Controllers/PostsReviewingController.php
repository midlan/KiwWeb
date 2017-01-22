<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class PostsReviewingController extends PostsController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo list of my posts assigned to me to review
        //show only undecided
    }
    
    public function addAction() {
        
        //todo formulář na přidání recenze
    }
    
    public function editAction() {
        
        //todo edit of post
        //lze editovat jen undecided recenzi
    }
    
    public function saveAction() {
        
        //todo uložení recenze
        //lze editovat jen undecided recenzi
    }

    public function getRequiredRole(): int {
        return \KivWeb\Role::REVIEWER;
    }

}