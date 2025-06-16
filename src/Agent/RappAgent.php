<?php

namespace App\Agent;

use App\Entity\VectorStore;
use App\Traits\IdentityTrait;
use Doctrine\ORM\EntityManagerInterface;
use Inspector\Inspector;
use NeuronAI\Agent;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Ollama\Ollama;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\Embeddings\VoyageEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\Doctrine\DoctrineEmbeddingEntityBase;
use NeuronAI\RAG\VectorStore\Doctrine\DoctrineVectorStore;
use NeuronAI\RAG\VectorStore\FileVectorStore;
use NeuronAI\RAG\VectorStore\MemoryVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;
class RappAgent extends MeiliRAG
{
    use IdentityTrait;

    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(
            background: ["You are an expert in Rappahannock Country, Virginia, and respond with data from the newspaper"],
            steps: [
//                "fetch the text from a URL, or ask the user to provide one.",
                "Use the tools you have available to retrieve news stories",
//                "Write the summary.",
            ],
            output: [
                "include the date and a summary with the response",
            ]
        );
    }


}
