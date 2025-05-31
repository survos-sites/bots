<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Dto\Person;
use Inspector\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
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
        #[Option('test structured message')] ?bool $structured=null
	): int
	{
        $agent = $this->chatAgent;
        if ($structured) {
            $structuredMessage = new UserMessage("I'm John and I like pizza!");

            $response = $agent->chat($structuredMessage);
            $io->writeln(json_encode($response));

            // Talk to the agent requiring the structured output
            try {
                $person = $agent
                    ->structured(
                        $structuredMessage,
                        Person::class
                    );
                dump($person);
            } catch (AgentException $exception) {
                $io->error($exception->getMessage());
                $io->writeln(":-(");
                return Command::FAILURE;
            }
        }

        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            $stream = $agent->stream($message);
            // Print the response chunk-by-chunk in real-time
            foreach ($stream as $idx => $message) {
                dump($idx, $message);
            }
//            $io->write($stream);
//            dump($response->getUsage());
            $msg = $io->ask('You');
        } while ($msg);
        return Command::SUCCESS;
	}


}
