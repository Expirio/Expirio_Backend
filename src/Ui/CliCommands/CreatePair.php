<?php

namespace App\Ui\CliCommands;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Infrastructure\SlotsManagerRedis;
use Predis\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePair extends Command
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
			->setName('pair:create')
			->setDescription('Create new pair slots')
			->setHelp('Example: bin/console pair:create --password=sesame1 --expire_in=PT5S')
			->addOption('password', null, InputOption::VALUE_REQUIRED, 'Password to read secret from read slot')
			->addOption('expire_in', null, InputOption::VALUE_REQUIRED, 'Expiration time');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$createPairCommand = new CreatePairSlotsCommand(
			Uuid::uuid4()->toString(),
			Uuid::uuid4()->toString(),
			$input->getOption('password'),
			$input->getOption('expire_in')
		);

		$pair = $this->handler->handle($createPairCommand);

		echo "\nRead slot: " . $pair->getReadUid() . "\n";
		echo "\nWrite slot: " .  $pair->getWriteUid() ."\n";

	}
}