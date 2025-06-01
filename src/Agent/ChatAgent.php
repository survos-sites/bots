<?php

namespace App\Agent;

use App\Traits\IdentityTrait;
use NeuronAI\Agent;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class ChatAgent extends Agent
{
    use IdentityTrait;
    public function __construct(
        #[Autowire('%env(OPENAI_API_KEY)%')] private string $openApiKey
    )
    {
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
            background: ["You are an friend named Bob"],
//            steps: [
//                "Get the url of a YouTube video, or ask the user to provide one.",
//                "Use the tools you have available to retrieve the transcription of the video.",
//                "Write the summary.",
//            ],
//            output: [
//                "Write a summary in a paragraph without using lists. Use just fluent text.",
//                "After the summary add a list of three sentences as the three most important takeaways from the video.",
//            ]
        );

    }

    public function instructions(): string
    {
        return $this->getSystemPrompt()->__toString();
    }

    protected function tools(): array
    {
        return [];
        return [
            Tool::make(
                'finish_code',
                'Finish this Symfony console command',
            )->addProperty(
                new ToolProperty(
                    name: 'video_url',
                    type: 'string',
                    description: 'The URL of the YouTube video.',
                    required: true
                )
            )->setCallable(function (string $video_url) {
                return $video_url;
            })
        ];
    }
}
