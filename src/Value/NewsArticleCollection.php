<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use IteratorAggregate;
use JetBrains\PhpStorm\Immutable;
use JsonSerializable;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;

#[Immutable]
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

    /*
     * jsonSerialize wird automatisch durch json_encode aufgerufen, sodass dieses Objekt direkt in die JSON-Response
     * gegeben werden kann
     */
    public function jsonSerialize(): array
    {
        return array_map(static fn (NewsArticle $article) => $article->asShortenedArray(), $this->newsArticles);
    }
}
