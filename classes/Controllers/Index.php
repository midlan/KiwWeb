<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

class Index extends BaseController {
    
    public function indexAction() {
        
        $twig = $this->getApp()->getTwig();
        
        $template = $twig->load('skeleton.twig');
        
        echo $template->render(array('a_variable' => 'Hello world!'));
    }

    public function getRequiredRole(): int {
        return \KivWeb\Role::NONE;
    }

}