<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

/**
 * @covers \Trimethylpentan\NewsArticles\Entity\NewsArticle
 */
class NewsArticleTest extends TestCase
{
    public function testCanCreateNew(): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString('The text');
        $createdDate = new DateTimeImmutable();
        
        $newsArticle = NewsArticle::createNew($title, $text, $createdDate, $articleId);
        $this->assertSame($articleId, $newsArticle->getArticleId());
        $this->assertSame($title, $newsArticle->getTitle());
        $this->assertSame($text, $newsArticle->getText());
        $this->assertSame($createdDate, $newsArticle->getCreatedDate());
    }

    public function testCanCreateFromDatabaseRow(): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString('The text');
        $createdDate = (new DateTimeImmutable())->setTime(12, 0);

        $row = [
            'id'           => $articleId->asInt(),
            'title'        => $title->asString(),
            'text'         => $text->asString(),
            'created_date' => $createdDate->format('Y-m-d H:i:s'),
        ];
        
        $newsArticle = NewsArticle::fromDatabaseRow($row);
        $this->assertEquals($articleId, $newsArticle->getArticleId());
        $this->assertEquals($title, $newsArticle->getTitle());
        $this->assertEquals($text, $newsArticle->getText());
        $this->assertEquals($createdDate, $newsArticle->getCreatedDate());
    }

    public function testCanSetTitle(): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString('The text');
        $createdDate = new DateTimeImmutable();

        $newsArticle = NewsArticle::createNew($title, $text, $createdDate, $articleId);
        $newTitle = Title::fromString('New title');
        $newsArticle->setTitle($newTitle);
        $this->assertSame($newTitle, $newsArticle->getTitle());
    }

    public function testCanSetText(): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString('The text');
        $createdDate = new DateTimeImmutable();

        $newsArticle = NewsArticle::createNew($title, $text, $createdDate, $articleId);
        $newText = Text::fromString('New text');
        $newsArticle->setText($newText);
        $this->assertSame($newText, $newsArticle->getText());
    }

    public function testCanGetAsArray(): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString('The text');
        $createdDate = new DateTimeImmutable();

        $newsArticle = NewsArticle::createNew($title, $text, $createdDate, $articleId);
        
        $expected = [
            'id'          => $articleId->asInt(),
            'title'       => $title->asString(),
            'text'        => $text->asString(),
            'createdDate' => $createdDate->format(DateTimeInterface::ATOM),
        ];
        
        $this->assertSame($expected, $newsArticle->asArray());
    }

    /**
     * @dataProvider textProvider
     */
    public function testCanGetAsShortenedArray(string $originalText, string $shortenedText): void
    {
        $articleId   = ArticleId::fromInt(1);
        $title       = Title::fromString('The title');
        $text        = Text::fromString($originalText);
        $createdDate = new DateTimeImmutable();

        $newsArticle = NewsArticle::createNew($title, $text, $createdDate, $articleId);

        $expected = [
            'id'          => $articleId->asInt(),
            'title'       => $title->asString(),
            'text'        => $shortenedText,
            'createdDate' => $createdDate->format(DateTimeInterface::ATOM),
        ];

        $this->assertSame($expected, $newsArticle->asShortenedArray());
    }

    public function textProvider(): array
    {
        $longText = <<<LONG_TEXT
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et 
        dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
        Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet,
        consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
        no sea takimata sanctus est Lorem ipsum dolor sit amet.
        LONG_TEXT;
        
        $shortenedText = <<<SHORTENED
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et 
        dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
        Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lor...
        SHORTENED;

        
        return [
            ['this is the text', 'this is the text'],
            [$longText, $shortenedText]
        ];
    }
}
