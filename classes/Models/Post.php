<?php

declare(strict_types=1);

namespace KivWeb\Models;


class Post extends BaseModel {
    
    const MIN_REVIEWS_FOR_APPROVE = 3;
    
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

    public function setPostId(int $postId) {
        $this->postId = $postId;
    }

    public function setUserId(int $userId) {
        $this->userId = $userId;
    }

    public function setTitle(string $title) {
        $this->title = $title;
    }

    public function setAbstract(string $abstract) {
        $this->abstract = $abstract;
    }

    public function setPdf(string $pdf) {
        $this->pdf = $pdf;
    }

    public function setApproved(int $approved) {
        $this->approved = $approved;
    }

    public function clear() {
        $this->postId = null;
        $this->userId = null;
        $this->title = null;
        $this->abstract = null;
        $this->pdf = null;
        $this->approved = null;
    }
    
    public function fetchInto(array $data) {
        
        $this->clear();
        
        $this->setPostId((int)$data['post_id']);
        $this->setUserId((int)$data['user_id']);
        $this->setTitle($data['title']);
        $this->setAbstract((int)$data['abstract']);
        $this->setPdf((int)$data['pdf']);
        
        if($data['approved'] !== null) {
            $this->setApproved($data['approved']);
        }
    }
    
    public function canBeApproved(): bool {
        throw new Exception('not implented yet');
        //todo select at least 3 reviews
        self::MIN_REVIEWS_FOR_APPROVE;
    }
    
    public function assignToReviewBy(int $userId): bool {
        throw new Exception('not implented yet');
        
        if($this->getApproved() === null) {
            //todo insert reviewers_assign
        
            //todo on duplicate key ignore
        }
        
        
    }
    
    public function isAllowedToReviewBy(int $userId): bool {
        throw new Exception('not implented yet');
        
        //rozhodnuté příspěvky už nelze hodnotit
        if($this->getApproved() !== null) {
            return false;
        }
        
        //todo select from reviewers_assign
    }
    
    public function isLoaded(): bool {
        return $this->getPostId() !== null;
    }
    
    public function loadById(int $postId): bool {
        
        $this->clear();
        
        $stmt =  $this->getConnection()->prepare('SELECT * FROM posts WHERE post_id = :post_id LIMIT 1;');
        
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //příspěvek nalezen
        if($data !== false) {
            $this->fetchInto($data);
            return true;
        }
        
        return false;
    }
    
    public function save(): bool {
        
        $new = !$this->isLoaded();
        
        $query = $new ? 'INSERT INTO' : 'UPDATE';
        
        $query .= ' reviews SET '
            . 'post_id = :post_id,'
            . 'user_id = :user_id,'
            . 'title = :title,'
            . 'abstract = :abstract,'
            . 'pdf = :pdf,'
            . 'approved = :approved,';

        if(!$new) {
            $query .= ' WHERE post_id = :post_id';
        }
        
        $query .= ';';
        
        $conn = $this->getConnection();
        
        $stmt = $conn->prepare($query);
        
        $success = $stmt->execute(array(
            ':post_id' => $this->getPostId(),
            ':user_id' => $this->getUserId(),
            ':title' => $this->getTitle(),
            ':abstract' => $this->getAbstract(),
            ':pdf' => $this->getPdf(),
            ':approved' => $this->getApproved(),
        ));
        
        if($success && $new) {
            $this->setPostId((int)$conn->lastInsertId());
        }
        
        return $success;
    }
    
    public function delete(): bool {
        
        if($this->isLoaded()) {
            return false;
        }
        
        $stmt =  $this->getConnection()->prepare('DELETE FROM posts WHERE post_id = :post_id LIMIT 1;');
        
        $stmt->bindParam(':post_id', $this->getPostId());
        return $stmt->execute();
        
    }
    
    private static function stmtToObjectArray(\PDOStatement $stmt): array {
        
        $posts = array();
        
        while(($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            
            $post = new self;
            $post->fetchInto($row);
            
            $posts[] = $post;
        }
        
        return $posts;
    }

    public static function getArrayByAuthorId(\PDO $conn, int $userId): array {
        
        $stmt = $conn->prepare('SELECT * FROM posts WHERE user_id = :user_id;');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return self::stmtToObjectArray($stmt);
    }
    
    public static function getArrayToReviewBy(\PDO $conn, int $userId): array {
        throw new Exception('not implented yet');
        //todo is assigned to user
        //todo approved IS NULL
    }
    
    public static function getArrayToAssign(\PDO $conn): array {
        throw new Exception('not implented yet');
        //todo approved IS NULL
    }
}
