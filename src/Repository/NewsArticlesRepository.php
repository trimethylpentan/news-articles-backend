<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Repository;

use DateTimeInterface;
use mysqli;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Value\NewsArticle;
use Trimethylpentan\NewsArticles\Value\NewsArticleCollection;

class NewsArticlesRepository
{
    public function __construct(
        private mysqli $mysqli,
    ) {}

    public function createNewsArticle(NewsArticle $article): void
    {
        // TODO: Errorhandling
        $sql = 'INSERT INTO news_articles (title, text, created_date) VALUES (?, ?, ?)';
        $statement = $this->mysqli->prepare($sql);
        
        $title = $article->getTitle();
        $text = $article->getText();
        $createdDate = $article->getCreatedDate()->format('Y-m-d H:i:s');
        $statement->bind_param('sss', $title, $text, $createdDate);
        if (!$statement->execute()) {
            throw new MysqliException($this->mysqli->error, $this->mysqli->errno);
        }
    }

    public function getNewsArticles(): NewsArticleCollection
    {
        $sql = 'SELECT * FROM news_articles';
        $statement = $this->mysqli->query($sql);
        
        $newsArticles = array_map(
            static fn (array $row) => NewsArticle::fromDatabaseRow($row),
            $statement->fetch_all(MYSQLI_ASSOC)
        );
        return NewsArticleCollection::createFromArray($newsArticles);
    }

    public function getNewsArticleForId(int $articleId): ?NewsArticle
    {
        $sql = 'SELECT * FROM news_articles WHERE `id` = ?';
        $statement = $this->mysqli->prepare($sql);
        $statement->bind_param('i', $articleId);
        $statement->bind_result($row);
        
        $statement->execute();
        
        return $statement->fetch() ? NewsArticle::fromDatabaseRow($row) : null;
    }

    public function updateNewsArticle(NewsArticle $article): void
    {
        // TODO: Errorhandling
        $articleId = $article->getId();
        $title     = $article->getTitle();
        $text      = $article->getText();
        
        $sql = 'UPDATE news_articles SET title = ?, text = ? WHERE `id` = ?';
        $statement = $this->mysqli->prepare($sql);
        $statement->bind_param('ssi', $title, $text, $articleId);
        $statement->execute();
    }
}
