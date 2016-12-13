<?php

namespace KivWeb\Controllers;

class Index extends BaseController {
    
    public function indexAction() {
        echo 'Heuréka';
    }

    public function getPermissionLevel() {
        return; //todo
    }

}