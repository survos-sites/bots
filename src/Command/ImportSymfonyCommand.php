<?php

namespace App\Command;

use App\Agent\SymfonyAgent;
use NeuronAI\RAG\DataLoader\StringDataLoader;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('import:symfony', 'Import the Symfony docs')]
class ImportSymfonyCommand
{
	public function __construct(
        private SymfonyAgent $agent,
    )
	{
	}


	public function __invoke(
		SymfonyStyle $io,
		#[Argument('The name of the downloaded file')]
		string $zipPath = 'data/7.3.zip',
		#[Option('limit the number of records imported')]
		int $limit = 50,
	): int
	{
        $io->warning('The entityManager MUST point to a vector-enabled postgres database');
        if (!file_exists($zipPath)) {
            $io->warning('Downloading ...');
            file_put_contents($zipPath, file_get_contents('https://github.com/symfony/symfony-docs/archive/refs/heads/7.3.zip'));
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $io->error("Unable to open ZIP archive: $zipPath");
            return Command::FAILURE;
        }

        $io->success("ZIP Archive opened: $zipPath");

        $progressBar = new ProgressBar($io, $zip->numFiles);
        $importCount = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $progressBar->advance();
            $stat = $zip->statIndex($i);
            $ext = pathinfo($stat['name'], PATHINFO_EXTENSION);
            if (!in_array($ext, ['rst', 'md'])) {
                continue;
            }
            $io->writeln(sprintf(" - %s (%d bytes)", $stat['name'], $stat['size']));
            $content = $zip->getFromIndex($i);
            $documents = StringDataLoader::for($content)->getDocuments();
            $this->agent->addDocuments($documents);
            $importCount++;


            if ($limit && ($i >= $importCount)) {
                break;
            }
        }

        $zip->close();
        $progressBar->finish();


		if ($limit) {
		    $io->writeln("Option limit: $limit");
		}
		$io->success(self::class . " success.");
		return Command::SUCCESS;
	}
}
