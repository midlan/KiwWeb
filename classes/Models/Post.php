<?php

declare(strict_types=1);

namespace KivWeb\Models;


class Post extends BaseModel {
    
    private $postId;
    private $userId; 
    private $title;
    private $abstract;
    private $pdf; 
    private $approved;
    
    public function getPostId() {
        return $this->postId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAbstract() {
        return $this->abstract;
    }

    public function getPdf() {
        return $this->pdf;
    }

    public function getApproved() {
        return $this->approved;
    }

    public function setPostId($postId) {
        $this->postId = $postId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setAbstract($abstract) {
        $this->abstract = $abstract;
    }

    public function setPdf($pdf) {
        $this->pdf = $pdf;
    }

    public function setApproved($approved) {
        $this->approved = $approved;
    }
    
    public function clear() {
        
    }

}
