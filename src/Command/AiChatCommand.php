<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\MeiliRAG;
use App\Dto\Person;
use App\Service\AgentRegistry;
use Inspector\Inspector;
use NeuronAI\AgentInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\RAG\RAG;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\Observability\AgentMonitoring;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

#[AsCommand('ai:chat', 'Basic chat bot')]
class AiChatCommand
{
	public function __construct(
        private ChatAgent $chatAgent,
        private Inspector $inspector,
        private AgentRegistry $agentRegistry,

        #[AutowireLocator(MeiliRAG::class, indexAttribute: 'key')]
        private ServiceCollectionInterface $agents,
    )
	{
//        dd($this->agents);
//        foreach ($this->agents->getProvidedServices() as $key => $agent) {
//            dd($key, $agent);
//        }
//        return array_keys($this->agents->getProvidedServices());
//        dd($this->agents);
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('agent code', name: 'agent')] ?string $agentCode=null,

		#[Argument('initial message')]
        ?string $msg="who are you and what are your skills?",
        #[Option('test structured message')] ?bool $structured=null
	): int
	{
        if (!$agentCode) {
            $agentCodes = iterator_to_array($this->agentRegistry->agents());
            $agentCode = $io->askQuestion(new ChoiceQuestion('Agent Code?', $agentCodes));
        }
        $agent = $this->agentRegistry->get($agentCode);
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
            $io->write($stream);
            $msg = $io->ask('You');
        } while ($msg);

        return Command::SUCCESS;
	}


}
