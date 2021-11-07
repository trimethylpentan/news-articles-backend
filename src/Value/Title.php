<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use InvalidArgumentException;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
/**
 * @codeCoverageIgnore
 */
final class Title
{
    private const MAX_LENGTH = 255;
    
    private string $title;
    
    private function __construct(string $title)
    {
        if (strlen($title) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('The title must not be longer than %d characters', self::MAX_LENGTH)
            );
        }
        
        $this->title = $title;
    }

    public static function fromString(string $title): self
    {
        return new self($title);
    }

    public function asString(): string
    {
        return $this->title;
    }
}
