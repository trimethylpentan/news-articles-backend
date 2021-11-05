<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class Text
{
    private function __construct(
        private string $text,
    ) {}

    public static function fromString(string $text): self
    {
        return new self($text);
    }

    public function asString(): string
    {
        return $this->text;
    }
}
