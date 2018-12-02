<?php

namespace App\Ui\CliCommands;

use App\Application\Service\CommandHandler;
use App\Application\Service\ReadSecretQuery;
use App\Infrastructure\SlotsManagerRedis;
use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReadSecret extends Command
{
	/** @var CommandHandler */
	private $handler;

	public function __construct()
	{
		parent::__construct();

		$this->handler = new CommandHandler(
			new SlotsManagerRedis(
				new Client([
					"host" => "localhost",
					"port" => 6379
				])
			)
		);
	}

	protected function configure()
	{
		$this
			->setName('pair:read')
			->setDescription('Read secret')
			->setHelp('Example: bin/console pair:read --readuid=<read uid> --password=<password>')
			->addOption('readuid', null, InputOption::VALUE_REQUIRED, 'read slot uid')
			->addOption('password', null, InputOption::VALUE_REQUIRED, 'your password secret');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$WriteSecretCommand = new ReadSecretQuery(
			$input->getOption('readuid'),
			$input->getOption('password')
		);

		print_r($this->handler->handle($WriteSecretCommand));
	}
}