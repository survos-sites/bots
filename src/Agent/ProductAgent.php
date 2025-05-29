<?php

namespace App\Agent;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\RAG\Embeddings\EmbeddingsProviderInterface;
use NeuronAI\RAG\Embeddings\OpenAIEmbeddingsProvider;
use NeuronAI\RAG\RAG;
use NeuronAI\RAG\VectorStore\MemoryVectoreStore;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\RAG\VectorStore\MeilisearchVectorStore;
class ProductAgent extends RAG
{

    public function __construct(
        #[Autowire('%env(OPENAI_API_KEY)%')] private string $openApiKey,
        #[Autowire('%env(MEILI_SERVER)%')] private string $meiliHost,
    )
    {
    }

    protected function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
        );
    }

    protected function vectorStore(): VectorStoreInterface
    {
        return new MemoryVectoreStore();
    }

//    protected function vectorStore(): VectorStoreInterface
//    {
//        dd('x');
//        return new MeilisearchVectoreStore(
////            key: 'Api',
//            indexUid: 'aa_vector_products',
//        // host: 'http://localhost:7700'
//        );
//    }
    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(
            background: ["You help us find products in the embedded product data "],
//            steps: [
//                "fetch the text from a URL, or ask the user to provide one.",
//                "Use the tools you have available to retrieve the content of the video.",
//                "Write the summary.",
//            ],
            output: [
                "include the SKU, title, category and price for each product.",
            ]
        );

    }

    public function embeddings(): EmbeddingsProviderInterface
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
