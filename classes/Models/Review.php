<?php

declare(strict_types=1);

namespace KivWeb\Models;


class Review extends BaseModel {
    
    private $reviewId;
    private $postId;
    private $userId;
    private $reviewDate;
    private $originality;
    private $gramar;
    private $topic;
    private $note;
    
    public function getReviewId() {
        return $this->reviewId;
    }

    public function getPostId() {
        return $this->postId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getReviewDate() {
        return $this->reviewDate;
    }

    public function getOriginality() {
        return $this->originality;
    }

    public function getGramar() {
        return $this->gramar;
    }

    public function getTopic() {
        return $this->topic;
    }

    public function getNote() {
        return $this->note;
    }

    public function setReviewId($reviewId) {
        $this->reviewId = $reviewId;
    }

    public function setPostId($postId) {
        $this->postId = $postId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setReviewDate($reviewDate) {
        $this->reviewDate = $reviewDate;
    }

    public function setOriginality($originality) {
        $this->originality = $originality;
    }

    public function setGramar($gramar) {
        $this->gramar = $gramar;
    }

    public function setTopic($topic) {
        $this->topic = $topic;
    }

    public function setNote($note) {
        $this->note = $note;
    }
    
    public function clear() {
        
    }

}
