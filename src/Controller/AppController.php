<?php

namespace App\Controller;

use App\Service\AgentService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    public function __construct(
        private AgentService $agentService,
    )
    {
    }

    #[Route('/', name: 'app_homepage')]
    #[Template('app/homepage.html.twig')]
    public function index(): Response|array
    {
        return [
            'agents' => $this->agentService->agentsByCode(),
        ];
    }
}
