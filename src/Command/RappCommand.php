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
use Twig\Environment;

#[AsCommand('ai:rapp', 'Ask about Rappahannock County, using the RappNews stories')]
class RappCommand
{
	public function __construct(
        private RappAgent $agent,
        private CacheInterface $cache,
        private Environment $twig,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,

		#[Argument('initial message')]
		?string $msg="who are you and what are your skills?",
        #[Option('embed the documents')] ?bool $embed=null,
        #[Option('limit the total')] int $limit = 10,

    ): int
	{

        if ($embed) {
            $total = 0;
            for ($i=1; $i<=1; $i++) {

            $url = 'https://ff.survos.com/api/articles?page=' . $i;
            $io->title($url);
            $key = md5($url);
            $products = $this->cache->get($key, fn(CacheItem $item) => json_decode(file_get_contents($url)));
            $progressBar = new ProgressBar($io, count($products->member));
            $templateString = "On {{ Date }} the article {{ Title }} was published at {{ url }}.
            The article said {{ Content }}";
            $template = $this->twig->createTemplate($templateString);

            foreach ($products->member as $data) {
                $progressBar->advance();
                $text = $template->render((array)$data);
                $documents = StringDataLoader::for($text)->getDocuments();
                $io->writeln($data->Date . '/' . $data->Title);
                $this->agent->embeddings()->embedDocuments($documents);
                if ($total++ > $limit) {
                    break;
                }
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
