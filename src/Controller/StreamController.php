<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class StreamController
{
    private function loadArticles(): \Generator {
        yield ['title' => 'Article 1'];
        yield ['title' => 'Article 2'];
        yield ['title' => 'Article 3'];
    }

    #[Route('/stream-json', name: 'app_stream_json')]
    public function __invoke(): Response|StreamedResponse
    {

        $response = new StreamedResponse();
        $tokens = ['Welcome', 'to', 'Symfony', '7.3,', 'streamed', 'with', 'PHP', '8.4!'];

//        // any method or function returning a PHP Generator
//        return new StreamedJsonResponse($this->loadArticles(), 200, [
//                'Content-Type' => 'text/event-stream',
//                'Cache-Control' => 'no-cache',
//                'Connection' => 'keep-alive',
//            ]
//        );
//
//        $response->headers->set('Content-Type', 'application/json');
//        $response->headers->set('X-Accel-Buffering', 'no');
//        $response->headers->set('Cache-Control', 'no-cache');
//
//        $response->setChunks($tokens);
//        $response->send();

        $response->setCallback(function () use ($tokens): void {
            foreach ($tokens as $token) {
                echo json_encode(['token' => $token]) . "\n";
                ob_flush();
                flush();
                usleep(300000);
            }
        });

        return $response;
    }
}
