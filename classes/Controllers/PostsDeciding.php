<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class PostsDeciding extends BaseController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        //todo list of undecided posts
    }
    
    public function assignAction() {
        
        //todo assign post to review
        //assignovat lze jen nerozhodnuté příspěvky
        //todo redirect to index
    }
    
    public function decideAction() {
        
        //todo zkontrolovat jestli má alespoň 3 recenze
        //todo schválit nebo zamítnout příspěvek
        //redirect to index on succes
    }

    public function getRequiredRole(): int {
        return \KivWeb\Role::ADMIN;
    }

}