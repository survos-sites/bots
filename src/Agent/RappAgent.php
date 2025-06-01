<?php

namespace App\Agent;

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
class RappAgent extends RAG
{
    use IdentityTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire('%env(OPENAI_API_KEY)%')] private string $openApiKey,
        #[Autowire('%env(MEILI_SERVER)%')] private string $meiliHost,
        #[Autowire('%env(MEILI_API_KEY)%')] private ?string $meilikey=null,
//        #[Autowire('%env(VOYAGE_API_KEY)%')] private ?string $voyageKey=null,
    )
    {
    }

    protected function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
        );
    }

    public function embeddings(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(
            key: $this->openApiKey, model: 'text-embedding-3-small'
        );
//        return new OllamaEmbeddingsProvider(model: 'all-minilm');
    }

//    public function embeddings(): EmbeddingsProviderInterface
//    {
//        return new VoyageEmbeddingsProvider(
//            key: $this->voyageKey,
//            model: 'voyage-3'
//        );
//    }

    public function vectorStore(): VectorStoreInterface
    {

        return new DoctrineVectorStore(
            entityManager: $this->entityManager,
            entityClassName: DoctrineEmbeddingEntityBase::class
        );

//        return new FileVectorStore(
//            directory: '/tmp/x',
//            topK: 4
//        );

        return new MeilisearchVectorStore(
            key: $this->meilikey,
            indexUid: 'aa_vector_products',
            host: $this->meiliHost,
        );
    }
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

    public function OPENAIembeddings(): EmbeddingsProviderInterface
    {
        return new OpenAIEmbeddingsProvider(
            key: $this->openApiKey, model: 'text-embedding-3-small'
        );
    }

    public function instructions(): string
    {
        return $this->getSystemPrompt()->__toString();
    }

}
