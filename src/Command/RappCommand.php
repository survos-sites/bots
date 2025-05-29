<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\RappAgent;
use App\Agent\SummaryAgent;
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

#[AsCommand('ai:rapp', 'Ask about Rappahannock County, using the RappNews stories')]
class RappCommand
{
	public function __construct(
        private RappAgent $agent,
        private CacheInterface $cache,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,
		#[Argument('initial message')]
		?string $msg="who are you and what are your skills?",
        #[Option('embed the documents')] ?bool $embed=null

    ): int
	{

        if ($embed) {
            for ($i=1; $i<=1; $i++) {

            $url = 'https://ff.survos.com/api/articles?page=' . $i;
            $io->title($url);
            $key = md5($url);
            $products = $this->cache->get($key, fn(CacheItem $item) => json_decode(file_get_contents($url)));
            $progressBar = new ProgressBar($io, count($products->member));
            foreach ($products->member as $data) {
                $progressBar->advance();
                $content = sprintf("Article at %s was published on %s with the title %s.
                The body of the articles is\n%s\n",
                    $data->url, $data->Date, $data->Title, $data->Content);
                $documents = StringDataLoader::for($content)->getDocuments();
                $this->agent->embeddings()->embedDocuments($documents);
            }
            $progressBar->finish();
            }
        }

        $agent = $this->agent;
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
            $response = $agent->chat($message);
            $msg = $io->ask($response->getContent());
        } while ($msg);

        return Command::SUCCESS;
	}


}
