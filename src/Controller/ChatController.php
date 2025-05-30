<?php

namespace App\Controller;

use App\Agent\ChatAgent;
use App\Agent\ProductAgent;
use App\Agent\RappAgent;
use App\Agent\SummarizeAgent;
use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    const AGENTS = ['chat', 'rapp', 'product', 'summarize'];
    public function __construct(
        private ChatAgent $chatAgent,
        private RappAgent $rappAgent,
        private ProductAgent $productAgent,
        private SummarizeAgent $summarizeAgent,
    ) {

    }

    private function getAgent(string $agentCode): Agent
    {
        return match ($agentCode) {
            'chat' => $this->chatAgent,
            'rapp' => $this->rappAgent,
            'product' => $this->productAgent,
            'summarize' => $this->summarizeAgent,
        };
    }

    #[Route('/{agentCode}', name: 'chat_index')]
    public function index(string $agentCode): Response
    {
        $agent = $this->getAgent($agentCode);
        return $this->render('chat/index.html.twig', [
            'agent' => $agent,
            'agents' => self::AGENTS, // for the menu, ugh
            'agentCode' => $agentCode,
        ]);
    }

    #[Route('/api/chat/{agentCode}', name: 'chat_api')]
    public function chat(Request $request, string $agentCode): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        $agent = $this->getAgent($agentCode);
        $response = $agent->chat(new UserMessage($message));

//        $client = HttpClient::create();
//        $apiKey = $_ENV['OPENAI_API_KEY'];
//
//        $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
//            'headers' => [
//                'Authorization' => "Bearer $apiKey",
//                'Content-Type' => 'application/json',
//            ],
//            'json' => [
//                'model' => 'gpt-3.5-turbo',
//                'messages' => [
//                    ['role' => 'user', 'content' => $message],
//                ],
//            ],
//        ]);
//
//        $result = $response->toArray(false);
//
        return $this->json([
            'response' => $response->getContent()
        ]);
    }
}
