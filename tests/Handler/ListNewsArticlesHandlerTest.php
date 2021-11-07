<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response as Psr7Response;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\NewsArticleCollection;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

/**
 * @covers \Trimethylpentan\NewsArticles\Handler\ListNewsArticlesHandler
 */
class ListNewsArticlesHandlerTest extends TestCase
{
    private MockObject | NewsArticlesRepository $articlesRepository;
    private MockObject | ServerRequest $request;
    private Response $response;
    private ListNewsArticlesHandler $handler;
    
    protected function setUp(): void
    {
        $this->articlesRepository = $this->createMock(NewsArticlesRepository::class);
        $this->request            = $this->createMock(ServerRequest::class);
        $this->response           = new Response(new Psr7Response(), new StreamFactory());
        
        $this->handler = new ListNewsArticlesHandler($this->articlesRepository);
    }

    public function testCanGetNewsArticlesResponse(): void
    {
        $newsArticles = NewsArticleCollection::createFromArray([NewsArticle::createNew(
            Title::fromString('The title'),
            Text::fromString('The text'),
            new DateTimeImmutable('today'),
            ArticleId::fromInt(1),
        )]);
        
        $this->articlesRepository->expects($this->once())->method('getNewsArticles')->willReturn($newsArticles);
        /** @var Response $response */
        $response = ($this->handler)($this->request, $this->response, []);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode(['news-articles' => $newsArticles], JSON_THROW_ON_ERROR), $response->getBody()->getContents());
    }
}
