<?php

namespace App\Command;

use App\Agent\ChatAgent;
use Inspector\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\Observability\AgentMonitoring;

#[AsCommand('ai:chat', 'Basic chat bot')]
class AiChatCommand
{
	public function __construct(
        private ChatAgent $chatAgent,
        private Inspector $inspector,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('initial message')]
        ?string $msg="who are you and what are your skills?",
	): int
	{
        $io->writeln($this->inspector::class);
        $agent = $this->chatAgent;
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            $response = $agent->chat($message);
//            dump($response->getUsage());
            $msg = $io->ask($response->getContent());
        } while ($msg);
        return Command::SUCCESS;
	}


}
