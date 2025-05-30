<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\SummarizeAgent;
use App\Agent\SummaryAgent;
use Inspector\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\RAG\DataLoader\StringDataLoader;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\Observability\AgentMonitoring;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand('ai:summarize', 'Summarize an article')]
class SummarizeCommand
{
	public function __construct(
        private SummarizeAgent $agent,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('initial message')]
		?string $msg="who are you and what are your skills?",
	): int
	{

        $agent = $this->agent;
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            try {
                $urlAction =$agent->structured($message);
                dump($urlAction);
            } catch (\Exception $exception) {
                $io->warning("not a structured message: " . $msg);
            }

            $response = $agent->chat($message);
//            dump($response->getUsage());
            $msg = $io->ask($response->getContent());
        } while ($msg);
        return Command::SUCCESS;
	}


}
