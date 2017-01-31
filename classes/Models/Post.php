<?php

declare(strict_types=1);

namespace KivWeb\Models;

use \KivWeb\App;

class Post extends BaseModel {
    
    const MIN_REVIEWS_FOR_APPROVE = 3;
    
    private $postId;
    private $userId; 
    private $title;
    private $abstract;
    private $pdf; 
    private $approved;
    private $markOriginality;
    private $markGramar;
    private $markTopic;
    private $authorName;
    
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

    public function getMarkOriginality() {
        return $this->markOriginality;
    }

    public function getMarkGramar() {
        return $this->markGramar;
    }

    public function getMarkTopic() {
        return $this->markTopic;
    }

    public function getAuthorName() {
        return $this->authorName;
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
    
    public function setMarkOriginality(int $markOriginality) {
        $this->markOriginality = $markOriginality;
    }

    public function setMarkGramar(int $markGramar) {
        $this->markGramar = $markGramar;
    }

    public function setMarkTopic(int $markTopic) {
        $this->markTopic = $markTopic;
    }

    public function setAuthorName(string $authorName) {
        $this->authorName = $authorName;
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
        $this->markOriginality = null;
        $this->markGramar = null;
        $this->markTopic = null;
        $this->authorName = null;
    }
    
    public function fetchInto(array $data) {
        
        if(array_key_exists('post_id', $data)) {
            $this->setPostId((int)$data['post_id']);
        }
        
        if(array_key_exists('user_id', $data)) {
            $this->setUserId((int)$data['user_id']);
        }
        
        if(array_key_exists('title', $data)) {
            $this->setTitle((string)$data['title']);
        }
        
        if(array_key_exists('abstract', $data)) {
            $this->setAbstract((string)$data['abstract']);
        }
        
        if(array_key_exists('pdf', $data)) {
            $this->setPdf((string)$data['pdf']);
        }
        
        if(array_key_exists('approved', $data) && $data['approved'] !== null) {
            $this->setApproved($data['approved']);
        }
        
        if(array_key_exists('mark_originality', $data)) {
            $this->setMarkOriginality((int)$data['mark_originality']);
        }
        
        if(array_key_exists('mark_gramar', $data)) {
            $this->setMarkGramar((int)$data['mark_gramar']);
        }
        
        if(array_key_exists('mark_topic', $data)) {
            $this->setMarkTopic((int)$data['mark_topic']);
        }
        
        if(array_key_exists('author_name', $data)) {
            $this->setAuthorName((string)$data['author_name']);
        }
    }
    
    public function canBeApproved(): bool {
        
        $stmt = $this->getConnection()->prepare('SELECT COUNT(*) FROM reviews WHERE post_id = :post_id GROUP BY post_id;');
        
        $stmt->bindParam(':post_id', $this->getPostId());
        $stmt->execute();
        
        $reviewsCount = $stmt->fetchColumn();
        
        if($reviewsCount === false) {
            return false;
        }
        
        if($reviewsCount < self::MIN_REVIEWS_FOR_APPROVE) {
            return false;
        }
        
        return true;
    }
    
    public function assignToReviewBy(int $userId): bool {
        
        //rozhodnuté příspěvky už nelze přiřazovat
        if($this->isLoaded() && $this->getApproved() === null) {
            
            $stmt = $this->getConnection()->prepare('INSERT INTO reviewers_assign VALUES (:user_id, :post_id) ON DUPLICATE KEY UPDATE user_id = user_id;');
        
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':post_id', $this->getPostId());
            return $stmt->execute();
        }
        
        return false;
    }
    
    public function isAllowedToReviewBy(int $userId): bool {
        
        //rozhodnuté příspěvky už nelze hodnotit
        if($this->isLoaded() && $this->getApproved() === null) {
            
            $stmt = $this->getConnection()->prepare('SELECT 1 FROM reviewers_assign WHERE post_id = :post_id AND user_id = :user_id LIMIT 1;');
        
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':post_id', $this->getPostId());
            $stmt->execute();
            
            return $stmt->fetchColumn() !== false;
        }
        
        return false;
    }
    
    public function isLoaded(): bool {
        return $this->getPostId() !== null;
    }
    
    public function loadById(int $postId): bool {
        
        $this->clear();
        
        $stmt =  $this->getConnection()->prepare('SELECT p.*, u.name author_name FROM posts p JOIN users u USING(user_id) WHERE post_id = :post_id LIMIT 1;');
        
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        //příspěvek nalezen
        if($data !== false) {
            $this->fetchInto($data);
            return $this->isLoaded();
        }
        
        return false;
    }
    
    public function save(): bool {
        
        $new = !$this->isLoaded();
        
        $query = $new ? 'INSERT INTO' : 'UPDATE';
        
        $query .= ' posts SET '
            . 'post_id = :post_id,'
            . 'user_id = :user_id,'
            . 'title = :title,'
            . 'abstract = :abstract,'
            . 'pdf = :pdf,'
            . 'approved = :approved';

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
        
        if(!$this->isLoaded()) {
            return false;
        }
        
        $stmt = $this->getConnection()->prepare('DELETE FROM posts WHERE post_id = :post_id LIMIT 1;');
        
        $stmt->bindParam(':post_id', $this->getPostId());
        return $stmt->execute();
        
    }
    
    private static function stmtToObjectArray(App $app, \PDOStatement $stmt): array {
        
        $posts = array();
        
        while(($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            
            $post = new self($app);
            $post->fetchInto($row);
            
            $posts[] = $post;
        }
        
        return $posts;
    }

    public static function getArrayByAuthorId(App $app, int $userId): array {
        
        $stmt = $app->getConnection()->prepare('SELECT p.*, AVG(originality) mark_originality, AVG(gramar) mark_gramar, AVG(topic) mark_topic FROM posts p LEFT JOIN reviews r USING(post_id) WHERE p.user_id = :user_id GROUP BY post_id;');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return self::stmtToObjectArray($app, $stmt);
    }
    
    public static function getArrayToReviewBy(App $app, int $userId): array {
        
        $stmt = $app->getConnection()->prepare('SELECT p.*, u.name author_name FROM posts p JOIN reviewers_assign a USING(post_id) JOIN users u ON p.user_id = u.user_id WHERE p.approved IS NULL AND a.user_id = :user_id;');
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return self::stmtToObjectArray($app, $stmt);
    }
    
    public static function getArrayToAssign(App $app): array {
        
        $stmt = $app->getConnection()->prepare('SELECT p.*, u.name author_name, AVG(originality) mark_originality, AVG(gramar) mark_gramar, AVG(topic) mark_topic FROM posts p JOIN users u USING(user_id) LEFT JOIN reviews r USING(post_id) WHERE approved IS NULL GROUP BY post_id;');
        
        $stmt->execute();
        
        return self::stmtToObjectArray($app, $stmt);
    }
    
    public static function getArrayApproved(App $app): array {
        
        $stmt = $app->getConnection()->prepare('SELECT * FROM posts WHERE approved = 1;');
        
        $stmt->execute();
        
        return self::stmtToObjectArray($app, $stmt);
    }
}
