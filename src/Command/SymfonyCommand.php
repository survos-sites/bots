<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\RappAgent;
use App\Agent\SymfonyAgent;
use Inspector\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\RAG\DataLoader\StringDataLoader;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\Observability\AgentMonitoring;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

#[AsCommand('ai:symfony', 'Ask about Symfony')]
class SymfonyCommand
{
	public function __construct(
        private SymfonyAgent $agent,
        private CacheInterface $cache,
        private Environment $twig,
        private Inspector $inspector,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,

		#[Argument('initial message')]
		?string $msg="who are you and what are your skills?",
        #[Option('chat (instead of answer)')] ?bool $chat=null,
        #[Option('limit the total')] int $limit = 10,

    ): int
	{

        $agent = $this->agent;
        $agent->observe(new AgentMonitoring($this->inspector));
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            if ($chat) {
                $io->title("Chatting");
                $io->writeln($agent->chat($message)->getContent());
            }
            $io->title("Answering");
            $response = $agent->answer($message);
            $msg = $io->ask($response->getContent());
            $this->inspector->flush();
        } while ($msg);

        return Command::SUCCESS;
	}


}
