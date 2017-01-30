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
        
        $app = $this->getApp();
        
        if(isset($_POST['post_id'])) {
            
            $post = new \KivWeb\Models\Post;
            
            $post->loadById((int)$_POST['post_id']);
            
            //smazání příspěvku
            if($post->delete()) {
                $app->addMessage('Smazání příspěvku bylo úspěšné');
            }
            else {
                $app->addMessage('Smazání příspěvku se nezdařilo');
            }
            
        }
        
        //přesměrování
        header('Location: ' . $app->getRouter()->buildUrl('postsAuthoring'), true, 302);
    }
    

    public function getRequiredRole(): int {
        return \KivWeb\Models\User::ROLE_AUTHOR;
    }

}