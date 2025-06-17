<?php

namespace App\Agent;

use App\Dto\Person;
use App\Traits\IdentityTrait;
use NeuronAI\Agent;
use NeuronAI\AgentInterface;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\SystemPrompt;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


#[AsTaggedItem('chat')]
class ChatAgent extends AppAgent implements AppAgentInterface
{
    use IdentityTrait;

    protected function provider(): AIProviderInterface
    {
        return new OpenAI($this->openApiKey,
            'gpt-4.1-nano'
        );
    }

    protected function getOutputClass(): string
    {
        return Person::class;
    }

    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(
            background: [
                "You are an friend named Bob",
                "I am a programmer"
            ],
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
