<?php

namespace App\Command;

use App\Agent\ChatAgent;
use App\Agent\RappAgent;
use App\Message\EmbedMessage;
use Inspector\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\RAG\DataLoader\StringDataLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use NeuronAI\Observability\AgentMonitoring;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

#[AsCommand('ai:rapp', 'Ask about Rappahannock County, using the RappNews stories')]
class RappCommand
{
	public function __construct(
        private RappAgent $agent,
        private CacheInterface $cache,
        private Environment $twig,
        private Inspector $inspector,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    )
	{
	}

	public function __invoke(
		SymfonyStyle $io,

		#[Argument('initial message')]
		?string $msg="who are you and what are your skills?",
        #[Option('embed the documents')] ?bool $embed=null,
        #[Option('chat (instead of answer)')] ?bool $chat=null,
//        #[Option('limit the total')] int $limit = 500,
        #[Option('number of pages')] int $pages = 100

    ): int
	{

        if ($embed) {
            $templateString = <<< END
On <time datetime="{{ publishedTime|format_date('short') }}">{{ publishedTime|format_date }}</time>
the Rappahannock News published an article titled '{{ headline }}'
{{ content|raw }}

Citation: {{ url }}
END;
            $template = $this->twig->createTemplate($templateString);

            $total = 0;
            for ($i=1; $i<=$pages; $i++) {
//                https://ff.survos.com/api/articles?page=1&itemsPerPage=30&order%5BpublishedTime%5D=desc&order%5Bbyline%5D=asc&order%5Bsection%5D=asc
            $url = 'https://ff.survos.com/api/articles?order%5BpublishedTime%5D=desc&page=' . $i;
            $io->title($url);
            $key = md5($url);
            $products = $this->cache->get($key, fn(CacheItem $item) => json_decode(file_get_contents($url)));
            $progressBar = new ProgressBar($io, count($products->member));

            foreach ($products->member as $data) {
                $progressBar->advance();
                $text = $template->render((array)$data);
                $text = strip_tags($text);
                $this->messageBus->dispatch(
                    new EmbedMessage($text, $data->id,  'rapp')
                );
//                foreach ($documents as $document) {
//                    $io->writeln(substr($document->content, 0, 100));
//                }
                $io->writeln($data->publishedTime . '/' . $data->headline);
//                $embedded = $this->agent->embeddings()->embedDocuments($documents);
//                $this->agent->vectorStore()->addDocuments($embedded);
//                $this->agent->vectorStore()->addDocuments($documents);
//                if ($total++ > $limit) {
//                    break;
//                }
            }


            $progressBar->finish();
            }
        }

        $agent = $this->agent;
        $agent->observe(new AgentMonitoring($this->inspector));
        $io->writeln($agent->instructions());
        do {
            $message = new UserMessage($msg);
//            $io->title("Chatting");
//            $io->writeln($agent->chat($message)->getContent());
            $io->title("Answering");
            $response = $agent->answer($message);
            $msg = $io->ask($response->getContent());
            $this->inspector->flush();
        } while ($msg);

        return Command::SUCCESS;
	}


}
