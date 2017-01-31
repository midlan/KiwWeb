<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

abstract class PostsController extends BaseController {
    
    protected function getDownloadingPost(): \KivWeb\Models\Post {
        
        $app = $this->getApp();
        $post = new \KivWeb\Models\Post($app);
        
        if(!isset($_GET['post_id'])) {
            $app->errorResponse(404, 'Missing post_id parameter in query.');
            return $post;
        }
        
        $post->loadById((int)$_GET['post_id']);

        if(!$post->isLoaded()) {
            $app->errorResponse(404, "Post with post_id {$_GET['post_id']} not found.");
            return $post;
        }
    }
    
    protected function outputPdf(\KivWeb\Models\Post $post) {
        
        header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: Binary');
        header("Content-disposition: attachment; filename=\"{$post->getTitle()}.pdf\""); //todo tady by to chtělo escapovat
        
        echo $post->getPdf();
    }

}