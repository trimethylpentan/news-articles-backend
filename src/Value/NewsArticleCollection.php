<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use IteratorAggregate;
use JsonSerializable;

final class NewsArticleCollection implements IteratorAggregate, JsonSerializable
{
    private array $newsArticles;
    
    private function __construct(NewsArticle ...$newsArticle)
    {
        $this->newsArticles = $newsArticle;
    }

    public static function createFromArray(array $newsArticles): self
    {
        return new self(...$newsArticles);
    }
    
    public function getIterator(): iterable
    {
        yield from $this->newsArticles;
    }

    public function jsonSerialize(): array
    {
        return array_map(static fn (NewsArticle $article) => $article->asArray(), $this->newsArticles);
    }
}
