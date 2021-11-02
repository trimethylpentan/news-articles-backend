<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Value;

use DateTimeImmutable;
use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;

final class NewsArticle
{
    private function __construct(
        private ?int $id,
        private string $title,
        private string $text,
        private DateTimeImmutable $createdDate,
    ) {}

    public static function createNew(string $title, string $text, DateTimeImmutable $createdDate): self
    {
        return new self(null, $title, $text, $createdDate);
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            (int)$row['id'],
            $row['title'],
            $row['text'],
            new DateTimeImmutable($row['created_date']),
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
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
            'id'          => $this->id,
            'title'       => $this->title,
            'text'        => $this->text,
            'createdDate' => $this->createdDate->format(DateTimeInterface::ATOM),
        ];
    }
}
