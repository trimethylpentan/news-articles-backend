<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

class NewsArticle
{
    private const PREVIEW_MAX_CHAR_COUNT = 300;
    
    private function __construct(
        private ?ArticleId        $articleId,
        private Title             $title,
        private Text              $text,
        private DateTimeImmutable $createdDate,
    ) {}

    public static function createNew(
        Title             $title,
        Text              $text,
        DateTimeImmutable $createdDate,
        ?ArticleId        $articleId = null,
    ): self {
        return new self($articleId, $title, $text, $createdDate);
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            ArticleId::fromInt((int)$row['id']),
            Title::fromString($row['title']),
            Text::fromString($row['text']),
            new DateTimeImmutable($row['created_date']),
        );
    }

    public function getArticleId(): ?ArticleId
    {
        return $this->articleId;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getText(): Text
    {
        return $this->text;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
    }

    public function setText(Text $text): void
    {
        $this->text = $text;
    }

    public function getCreatedDate(): DateTimeImmutable
    {
        return $this->createdDate;
    }

    #[ArrayShape([
        'id'          => 'int|null',
        'title'       => 'string',
        'text'        => 'string',
        'createdDate' => 'string',
    ])]
    public function asArray(): array
    {
        return [
            'id'          => $this->articleId->asInt(),
            'title'       => $this->title->asString(),
            'text'        => $this->text->asString(),
            'createdDate' => $this->createdDate->format(DateTimeInterface::ATOM),
        ];
    }

    #[ArrayShape([
        'id'          => 'int|null',
        'title'       => 'string',
        'text'        => 'string',
        'createdDate' => 'string',
    ])]
    public function asShortenedArray(): array
    {
        return [
            'id'          => $this->articleId->asInt(),
            'title'       => $this->title->asString(),
            'text'        => $this->shortenText($this->text->asString()),
            'createdDate' => $this->createdDate->format(DateTimeInterface::ATOM),
        ];
    }

    private function shortenText(string $text): string
    {
        if (strlen($text) <= self::PREVIEW_MAX_CHAR_COUNT) {
            return $text;
        }
        
        return substr($text, 0, self::PREVIEW_MAX_CHAR_COUNT) . '...';
    }
}
