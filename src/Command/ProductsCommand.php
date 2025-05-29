<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\ProductAgent;
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

#[AsCommand('ai:products', 'search the embedded products')]
class ProductsCommand
{
	public function __construct(
        private ProductAgent $agent,
        private CacheInterface $cache,
//        private Inspector $inspector,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('initial message')] string $msg="Who are you and what are your skills?",
        #[Option('embed the documents')] ?bool $embed=null
	): int
	{

        if ($embed) {
            $url = 'https://dummyjson.com/products?limit=5';
            $key = md5($url);
            $products = $this->cache->get($key, fn(CacheItem $item) => json_decode(file_get_contents($url)));
            foreach ($products->products as $product) {
                $description = $product->description;
                $description = sprintf("SKU %s is %s and can be described as %s.  It's price is %s and is a %s product.",
                $product->sku, $product->title, $description, $product->price, $product->category);
                $this->agent->embeddings()->embedText($description);
//                $documents = StringDataLoader::for($description)->getDocuments();
//                $this->agent->embeddings()->embedDocuments($documents);
            }
        }

        $agent = $this->agent;
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            $response = $agent->chat($message)->getContent();
            if (!json_validate($response)) {
                dd($response, json_decode($response, true));
            } else {

            }
//            dump($response->getUsage());
            $msg = $io->ask($response);
        } while ($msg);
        return Command::SUCCESS;
	}


}
