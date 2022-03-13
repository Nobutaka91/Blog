<?php 
class QueryArticle extends Connect {

    private $article;

    public function __construct(){
        parent::__construct();
    }

    public function setArticle(Article $article){
        $this->article = $article;
    }

    public function save(){
        if($this->article->getId()){
            // IDがあるときは上書き
        } else {
            // IDがなければ新規作成
            $title = $this->article->getTitle();
            $body = $this->article->getBody();
            $stmt = $this->dbh->prepare("INSERT INTO articles (title, body, created_at, updated_at)
                        VALUES (:title, :body, NOW(), NOW())");
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':body', $body, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    
    public function findAll(){
        $stmt = $this->dbh->prepare("SELECT * FROM articles");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $articles = array();
        foreach ($results as $result){
            $article = new Article();
            $article->setId($result['id']);
            $article->setTitle($result['title']);
            $article->setBody($result['body']);
            $article->setCreatedAt($result['created_at']);
            $article->setUpdatedAt($result['updated_at']);
            $articles[] = $article;
        }
        return $articles;
    }
}

?>