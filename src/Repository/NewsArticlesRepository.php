<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Repository;

use mysqli;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\NewsArticleCollection;

class NewsArticlesRepository
{
    public function __construct(
        private mysqli $mysqli,
    ) {}

    public function createNewsArticle(NewsArticle $article): void
    {
        $sql = 'INSERT INTO news_articles (title, text, created_date) VALUES (?, ?, ?)';
        $statement = $this->mysqli->prepare($sql);
        
        $title = $article->getTitle()->asString();
        $text = $article->getText()->asString();
        $createdDate = $article->getCreatedDate()->format('Y-m-d H:i:s');
        $statement->bind_param('sss', $title, $text, $createdDate);
        if (!$statement->execute()) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }
    }

    public function getNewsArticles(): NewsArticleCollection
    {
        $sql = 'SELECT * FROM news_articles';
        $result = $this->mysqli->query($sql);
        
        if ($result === false) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }
        
        $newsArticles = array_map(
            static fn (array $row) => NewsArticle::fromDatabaseRow($row),
            $result->fetch_all(MYSQLI_ASSOC)
        );
        return NewsArticleCollection::createFromArray($newsArticles);
    }

    public function getNewsArticleForId(ArticleId $articleId): ?NewsArticle
    {
        $boundArticleId = $articleId->asInt();
        $sql = 'SELECT * FROM news_articles WHERE `id` = ?';
        $statement = $this->mysqli->prepare($sql);
        $statement->bind_param('i', $boundArticleId);
        
        $statement->execute();
        $row = $statement->get_result()->fetch_array();
        
        return $row !== null ? NewsArticle::fromDatabaseRow($row) : null;
    }

    public function updateNewsArticle(NewsArticle $article): void
    {
        $articleId = $article->getArticleId()?->asInt();
        $title     = $article->getTitle()->asString();
        $text      = $article->getText()->asString();
        
        $sql = 'UPDATE news_articles SET title = ?, text = ? WHERE `id` = ?';
        $statement = $this->mysqli->prepare($sql);
        $statement->bind_param('ssi', $title, $text, $articleId);
        if (!$statement->execute()) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }
    }

    public function deleteNewsArticle(ArticleId $articleId): void
    {
        $boundArticleId = $articleId->asInt();
        
        $sql = 'DELETE FROM news_articles WHERE `id` = ?';
        $statement = $this->mysqli->prepare($sql);
        $statement->bind_param('i', $boundArticleId);
        if (!$statement->execute()) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }
    }
}
