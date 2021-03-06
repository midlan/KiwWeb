<?php

declare(strict_types=1);

namespace KivWeb\Controllers;

use \KivWeb\App,
    \KivWeb\Models\Post,
    \KivWeb\Models\Review;

class PostsReviewingController extends PostsController {
    
    public function indexAction() {
        
        $app = $this->getApp();
        $user = $app->getUser();
        $twig = $app->getTwig();
        
        $posts = Post::getArrayToReviewBy($app, $user->getUserId());
        $reviews = Review::getArrayByAuthor($app, $user->getUserId());
        
        $template = $twig->load('posts_reviewing.twig');
        
        echo $template->render(array(
            'posts' => $posts,
            'reviews' => $reviews,
        ));
    }
    
    public function addAction() {
        
        $app = $this->getApp();
        $twig = $app->getTwig();
        
        if(isset($_GET['post_id']) && isset($_GET['review_id'])) {
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        $existingReview = new Review($app);
        $existingReview->loadByPostAndReviewer($app->getUser()->getUserId(), (int)$_GET['post_id']);
        
        //recenze už  existuje, přesměrovat na edit
        if($existingReview->isLoaded()) {
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing', 'edit', array('review_id' => $existingReview->getReviewId())), true, 302);
            return;
        }
        
        $post = new Post($app);
        $post->loadById((int)$_GET['post_id']);

        if(!$post->isLoaded()) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit neexistuje.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }

        if(!$post->isAllowedToReviewBy($app->getUser()->getUserId())) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit Vám nebyl k hodnocení přidělen.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        $template = $twig->load('review.twig');
        
        echo $template->render(array(
            'review' => new Review($app),
            'post_id' => $post->getPostId(),
        ));
    }
    
    public function editAction() {
        
        $app = $this->getApp();
        
        $review = new Review($app);
        $review->loadById((int)$_GET['review_id']);
        
        if(!$review->isLoaded()) {
            $app->addMessage(App::MESSAGE_ERROR, 'Hodnocení které se pokoušíte upravit neexistuje.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
            
        $post = new Post($app);
        $post->loadById($review->getPostId());

        if(!$post->isLoaded()) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit neexistuje.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }

        if(!$post->isAllowedToReviewBy($app->getUser()->getUserId())) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit Vám nebyl k hodnocení přidělen.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        $twig = $app->getTwig();

        $template = $twig->load('review.twig');
        
        echo $template->render(array(
            'review' => $review,
            'post_id' => null,
        ));
    }
    
    public function saveAction() {
        
        $app = $this->getApp();
        $user = $app->getUser();
        
        if(!
            (isset($_POST['review_id']) or isset($_POST['post_id'])
            && isset($_POST['originality'])
            && isset($_POST['gramar'])
            && isset($_POST['topic'])
            && isset($_POST['note']))
        ) {
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        $review = new Review($app);
        $post = new Post($app);
        
        //editace hodnocení
        if(isset($_POST['review_id'])) {
            
            $review->loadById((int)$_POST['review_id']);
            
            if(!$review->isLoaded()) {
                $app->addMessage(App::MESSAGE_ERROR, 'Hodnocení, které se pokoušíte upravit neexistuje.');
                header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
                return;
            }
            
            $post->loadById($review->getPostId());
        }
        //nové hodnocení
        else {
            $post->loadById((int)$_POST['post_id']);
        }
        
        if(!$post->isLoaded()) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit neexistuje.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        if(!$post->isAllowedToReviewBy($user->getUserId())) {
            $app->addMessage(App::MESSAGE_ERROR, 'Přípěvek, který se pokoušíte hodnotit Vám nebyl k hodnocení přidělen nebo už bylo rozhodnuto o jeho zveřejnění.');
            header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
            return;
        }
        
        //nastavení dat
        $review->fetchInto($_POST);
        $review->setUserId($user->getUserId());
        $review->setReviewDate(date('Y-m-d H:i:s'));


        //uložení
        if($review->save()) {
            $app->addMessage(App::MESSAGE_SUCCESS, 'Hodnocení bylo uloženo.');
        }
        else {
            $app->addMessage(App::MESSAGE_ERROR, 'Hodnocení se nepodařilo uložit.');
        }
        
        header('Location: ' . $app->getRouter()->buildUrl('postsReviewing'), true, 302);
    }
    
    public function downloadAction() {
        
        $post = $this->getDownloadingPost();
        
        if(!$post->isLoaded()) {
            return; //headers generated by getDownloadingPost
        }
        
        if($post->isAllowedToReviewBy($this->getApp()->getUser()->getUserId())) {
            $app->errorResponse(403, 'Trying to access post not assigned to review.');
            return;
        }

        $this->outputPdf($post);
    }

    public function getRequiredRole(): int {
        return \KivWeb\Models\User::ROLE_REVIEWER;
    }

}