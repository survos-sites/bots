<?php
// https://dev.to/mongodb/building-a-chatbot-with-symfony-and-mongodb-5c8g
// BUT that uses MongoDB's vectorstore
namespace App\Agent;

use App\Traits\IdentityTrait;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\VoyageEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AppRAGAgent extends RAG implements AppAgentInterface
{
    use IdentityTrait;

    public function __construct(
        #[Autowire('%env(default::OPENAI_API_KEY)%')] protected string  $openApiKey,
        #[Autowire('%env(default::MEILI_SERVER)%')] protected string    $meiliHost,
        #[Autowire('%env(default::MEILI_API_KEY)%')] protected ?string  $meiliKey = null,
        #[Autowire('%env(default::VOYAGE_API_KEY)%')] protected ?string $voyageKey = null,
    )
    {
    }

    public function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-mini'
        );
    }

    public function embeddings(): EmbeddingsProviderInterface
    {
        return new VoyageEmbeddingsProvider(
            key: $this->voyageKey,
            model: 'voyage-3',
            dimensions: 1024
        );
    }

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
