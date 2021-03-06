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

    public function setReviewId(int $reviewId) {
        $this->reviewId = $reviewId;
    }

    public function setPostId(int $postId) {
        $this->postId = $postId;
    }

    public function setUserId(int $userId) {
        $this->userId = $userId;
    }

    public function setReviewDate(string $reviewDate) {
        $this->reviewDate = $reviewDate;
    }

    public function setOriginality(int $originality) {
        $this->originality = $originality;
    }

    public function setGramar(int $gramar) {
        $this->gramar = $gramar;
    }

    public function setTopic(int $topic) {
        $this->topic = $topic;
    }

    public function setNote(string $note) {
        
        if($note === '') {
            $this->note = null;
        }
        else {
            $this->note = $note;
        }
    }
    
    public function clear() {
        $this->reviewId = null;
        $this->postId = null;
        $this->userId = null;
        $this->reviewDate = null;
        $this->originality = null;
        $this->gramar = null;
        $this->topic = null;
        $this->note = null;
    }
    
    public function fetchInto(array $data) {
        
        if(array_key_exists('review_id', $data)) {
            $this->setReviewId((int)$data['review_id']);
        }
        
        if(array_key_exists('post_id', $data)) {
            $this->setPostId((int)$data['post_id']);
        }
        
        if(array_key_exists('user_id', $data)) {
            $this->setUserId((int)$data['user_id']);
        }
        
        if(array_key_exists('review_date', $data)) {
            $this->setReviewDate($data['review_date']);
        }
        
        if(array_key_exists('originality', $data)) {
            $this->setOriginality((int)$data['originality']);
        }
        
        if(array_key_exists('gramar', $data)) {
            $this->setGramar((int)$data['gramar']);
        }
        
        if(array_key_exists('topic', $data)) {
            $this->setTopic((int)$data['topic']);
        }
        
        if(array_key_exists('note', $data) && $data['note'] !== null) {
            $this->setNote($data['note']);
        }
    }
    
    public function isLoaded(): bool {
        return $this->getReviewId() !== null;
    }
    
    public function loadById(int $reviewId): bool {
        
        $this->clear();
        
        $stmt =  $this->getConnection()->prepare('SELECT * FROM reviews WHERE review_id = :review_id LIMIT 1;');
        
        $stmt->bindParam(':review_id', $reviewId);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //recenze nalezena
        if($data !== false) {
            $this->fetchInto($data);
            return $this->isLoaded();
        }
        
        return false;
    }
    
    public function loadByPostAndReviewer(int $reviewerId, int $postId): bool {
        
        $this->clear();
        
        $stmt =  $this->getConnection()->prepare('SELECT * FROM reviews WHERE user_id = :user_id AND post_id = :post_id LIMIT 1;');
        
        $stmt->bindParam(':user_id', $reviewerId);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //recenze nalezena
        if($data !== false) {
            $this->fetchInto($data);
            return $this->isLoaded();
        }
        
        return false;
    }
    
    public function save(): bool {
        
        $new = !$this->isLoaded();
        
        $query = $new ? 'INSERT INTO' : 'UPDATE';
        
        $query .= ' reviews SET '
            . 'review_id = :review_id,'
            . 'post_id = :post_id,'
            . 'user_id = :user_id,'
            . 'review_date = :review_date,'
            . 'originality = :originality,'
            . 'gramar = :gramar,'
            . 'topic = :topic,'
            . 'note = :note';

        if(!$new) {
            $query .= ' WHERE review_id = :review_id';
        }
        
        $query .= ';';
        
        $conn = $this->getConnection();
        
        $stmt = $conn->prepare($query);
        
        $success = $stmt->execute(array(
            ':review_id' => $this->getReviewId(),
            ':post_id' => $this->getPostId(),
            ':user_id' => $this->getUserId(),
            ':review_date' => $this->getReviewDate(),
            ':originality' => $this->getOriginality(),
            ':gramar' => $this->getGramar(),
            ':topic' => $this->getTopic(),
            ':note' => $this->getNote(),
        ));
        
        if($success && $new) {
            $this->setReviewId((int)$conn->lastInsertId());
        }
        
        return $success;
    }
    
    public static function getArrayByAuthor(\KivWeb\App $app, int $userId): array {
        
        $stmt = $app->getConnection()->prepare('SELECT * FROM reviews WHERE user_id = :user_id;');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $reviews = array();
        
        while(($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            
            $review = new self($app);
            $review->fetchInto($row);
            
            $reviews[] = $review;
        }
        
        return $reviews;
    }
}
