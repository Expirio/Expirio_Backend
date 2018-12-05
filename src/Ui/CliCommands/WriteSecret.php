<?php

namespace App\Ui\CliCommands;

use App\Application\Service\CommandHandler;
use App\Application\Service\WriteSecretCommand;
use App\Infrastructure\SlotsManagerRedis;
use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WriteSecret extends Command
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
			->setName('pair:write')
			->setDescription('Write secret')
			->setHelp('Example: bin/console pair:write --writeuid=<write uid> --secret=<my secret>')
			->addOption('writeuid', null, InputOption::VALUE_REQUIRED, 'write slot uid')
			->addOption('secret', null, InputOption::VALUE_REQUIRED, 'your secret');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$WriteSecretCommand = new WriteSecretCommand(
			$input->getOption('writeuid'),
			$input->getOption('secret')
		);

		$this->handler->handle($WriteSecretCommand);


		print_r('OK');
	}
}