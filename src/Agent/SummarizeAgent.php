<?php

namespace App\Agent;

use App\Dto\UrlAction;
use NeuronAI\Agent;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;


class SummarizeAgent extends Agent
{

    public function __construct(
        #[Autowire('%env(OPENAI_API_KEY)%')] private string $openApiKey,
        private CacheInterface $cache,
    )
    {
    }

    protected function getOutputClass(): string
    {
        return UrlAction::class;
    }

    protected function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
        );
    }

    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(
            background: ["You summarize news articles for children aged 10-12"],
            steps: [
                "fetch the text from a URL, or ask the user to provide one.",
                "Use the tools you have available to retrieve the content of the video.",
                "Write the summary.",
            ],
            output: [
                "Write a summary in a paragraph without using lists. Use just fluent text.",
                "After the summary add a list of three sentences as the three most important takeaways from the article.",
            ]
        );

    }

    public function instructions(): string
    {
        return $this->getSystemPrompt()->__toString();
    }

    protected function tools(): array
    {
        return [
            Tool::make(
                'fetch',
                'Fetch the article',
            )->addProperty(
                new ToolProperty(
                    name: 'article_url',
                    type: 'string',
                    description: 'The path to the article.',
                    required: true
                )
            )->setCallable(function (string $url): string {
                $content = $this->cache->get(md5($url), fn(CacheItem $item) => json_decode(file_get_contents($url)));
                return $content;
            })
        ];
    }
}

