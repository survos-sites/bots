<?php
// https://dev.to/mongodb/building-a-chatbot-with-symfony-and-mongodb-5c8g
// BUT that uses MongoDB's vectorstore
namespace App\Agent;

use App\Traits\IdentityTrait;
use NeuronAI\Agent;
use NeuronAI\AgentInterface;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AppAgent extends Agent implements AppAgentInterface, AgentInterface
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

    protected function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
        );
    }

    public function answer(...$arguments)
    {
        return $this->chat($arguments);
    }

    public function instructions(): string
    {
        return $this->getSystemPrompt()->__toString();
    }

}
