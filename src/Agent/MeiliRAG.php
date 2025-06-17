<?php
// https://dev.to/mongodb/building-a-chatbot-with-symfony-and-mongodb-5c8g
// BUT that uses MongoDB's vectorstore
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
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;

abstract class MeiliRAG extends AppRAGAgent implements AppAgentInterface
{
    use IdentityTrait;
//
//    public function __construct(
//        private EntityManagerInterface                         $entityManager,
//        #[Autowire('%env(OPENAI_API_KEY)%')] protected string  $openApiKey,
//        #[Autowire('%env(MEILI_SERVER)%')] protected string    $meiliHost,
//        #[Autowire('%env(MEILI_API_KEY)%')] protected ?string  $meiliKey = null,
//        #[Autowire('%env(VOYAGE_API_KEY)%')] protected ?string $voyageKey = null,
//    )
//    {
//        parent::__construct();
//    }

    public function vectorStore(): VectorStoreInterface
    {
        $index = new \ReflectionClass(static::class)->getShortName();
        return new MeilisearchVectorStore(
            key: $this->meiliKey,
            indexUid: $index,
            embedder: 'default',
            host: $this->meiliHost,
            topK: 5
        );
    }


    public function instructions(): string
    {
        return $this->getSystemPrompt()->__toString();
    }

}
