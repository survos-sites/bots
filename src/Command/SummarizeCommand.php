<?php

namespace App\Command;

use App\Agent\ChatAgent;
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
        private SummaryAgent $agent,
        private CacheInterface $cache,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('initial message')]
		?string $msg=null,
	): int
	{

        $url = 'https://dummyjson.com/products';
        $key = md5($url);
        $products = $this->cache->get($key, fn(CacheItem $item) => json_decode(file_get_contents($url)));
        foreach ($products->products as $product) {
            $description = $product->description;
            $documents = StringDataLoader::for($description)->getDocuments();
        }
        $this->agent->embeddings()->embedDocuments($documents);

        $agent = $this->agent;
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
