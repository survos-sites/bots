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
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;
#[AsTaggedItem('symfony')]
//#[AutoconfigureTag('app.agent')]
class SymfonyAgent extends MeiliRAG
{
    use IdentityTrait;

//    public function __construct(
//        private EntityManagerInterface                       $entityManager,
//        #[Autowire('%env(OPENAI_API_KEY)%')] private string  $openApiKey,
//        #[Autowire('%env(MEILI_SERVER)%')] private string    $meiliHost,
//        #[Autowire('%env(MEILI_API_KEY)%')] private ?string  $meiliKey=null,
//        #[Autowire('%env(VOYAGE_API_KEY)%')] private ?string $voyageKey=null,
//    )
//    {
//    }

    public function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
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
//        $dir = '/tmp/symfony';
//        if (!is_dir($dir)) {
//            mkdir($dir);
//        };
//        return new FileVectorStore(
//            directory: '/tmp/symfony',
//            topK: 4
//        );

//        return new DoctrineVectorStore(
//            entityManager: $this->entityManager,
//            entityClassName: VectorStore::class
//        );
        return new MeilisearchVectorStore(
            key: $this->meiliKey,
            indexUid: 'cc',
            embedder: 'default',
            host: $this->meiliHost,
            topK: 5
        );
    }

    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(
            background: ["You are an expert in Symfony, and have read all of the 7.3 docs"],
            steps: [
            ],
            output: [
                "include code examples when relevant.  Answer in conversational style.",
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
