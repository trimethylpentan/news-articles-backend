<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use InvalidArgumentException;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
/**
 * @codeCoverageIgnore
 */
final class ArticleId
{
    private int $articleId;
    
    public function __construct(int $articleId)
    {
        if ($articleId <= 0) {
            throw new InvalidArgumentException('ArticleId must be greater than 0');
        }
        
        $this->articleId = $articleId;
    }

    public static function fromInt(int $articleId): self
    {
        return new self($articleId);
    }

    public function asInt(): int
    {
        return $this->articleId;
    }
}
