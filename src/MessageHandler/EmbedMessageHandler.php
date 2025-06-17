<?php

namespace App\MessageHandler;

use App\Message\EmbedMessage;
use App\Service\AgentRegistry;
use NeuronAI\RAG\DataLoader\StringDataLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class EmbedMessageHandler
{
    public function __construct(
        private readonly AgentRegistry $agentRegistry,
        private LoggerInterface $logger,
    )
    {
    }

    public function __invoke(EmbedMessage $message): void
    {
        $agent = $this->agentRegistry->get($message->agentClass);
        $this->logger->info("chunking docs " . strlen($message->text));
        $documents = StringDataLoader::for($message->text)
//            ->withMaxLength(90)
            ->getDocuments();
        $this->logger->info("Adding documents " . count($documents));
        $agent->addDocuments($documents);
    }
}
