<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

/**
 * @covers \Trimethylpentan\NewsArticles\Handler\CreateNewsArticleHandler
 */
class CreateNewsArticleHandlerTest extends TestCase
{
    private MockObject | NewsArticlesRepository $articlesRepository;
    private MockObject | ServerRequest $request;
    private MockObject | Response $response;
    private CreateNewsArticleHandler $handler;
    
    protected function setUp(): void
    {
        $this->articlesRepository = $this->createMock(NewsArticlesRepository::class);
        $this->request            = $this->createMock(ServerRequest::class);
        $this->response           = $this->createMock(Response::class);
        
        $this->handler = new CreateNewsArticleHandler($this->articlesRepository);
    }

    public function testCanGetSuccessResponse(): void
    {
        $title = Title::fromString('The title');
        $text  = Text::fromString('Some text');
        
        $this->articlesRepository->expects($this->once())->method('createNewsArticle')
            ->willReturnCallback(function (NewsArticle $newsArticle) use ($text, $title) {
                $this->assertEquals($title, $newsArticle->getTitle());
                $this->assertEquals($text, $newsArticle->getText());
            });
        
        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'title' => $title->asString(),
            'text'  => $text->asString(),
        ], JSON_THROW_ON_ERROR));
        
        $this->response->expects($this->once())->method('withJson')->with(['success' => true], 200);
        $this->handler->__invoke($this->request, $this->response, []);
    }

    public function testExceptionResultsInErrorResponse(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'title' => 'The title',
            'text'  => 'The text',
        ], JSON_THROW_ON_ERROR));
        
        $this->articlesRepository->expects($this->once())->method('createNewsArticle')
            ->willThrowException(new MysqliException());
        
        $this->response->expects($this->once())->method('withJson')->with([
            'success' => false,
            'error'   => 'Es ist ein Fehler beim Erstellen des News-Artikel aufgetreten',
        ], 500);
        
        $this->handler->__invoke($this->request, $this->response, []);
    }
}
