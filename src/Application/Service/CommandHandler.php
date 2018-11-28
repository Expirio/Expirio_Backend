 <?php

namespace App\Application\Service;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;
use App\Infrastructure\SlotsManagerRedis;
use ReflectionClass;

class CommandHandler
{
	/** @var SlotsManagerRedis */
	private $redisManager;

	public function __construct(SlotsManagerRedis $redisManager)
	{
		$this->redisManager = $redisManager;
	}

	public function handle($command)
	{
		$handleMethod = 'handle' . (new ReflectionClass($command))->getShortName();
		return $this->$handleMethod($command);
	}

	private function handleCreatePairSlotsCommand(CreatePairSlotsCommand $command): PairSlot
	{
		$read = new ReadSlot($command->getReadUid(), $command->getReadPassword());
		$write = new WriteSlot($command->getWriteUid(), $command->getReadUid());

		$this->redisManager->persistSlot($write);
		$this->redisManager->persistSlot($read);

		return new PairSlot($read, $write);
	}

	private function handleWriteSecretCommand(WriteSecretCommand $command)
	{
		$writeSlot = $this->redisManager->fetchSlot($command->getWriteUid());
		
		if ($writeSlot && $writeSlot instanceof WriteSlot) {
			$readlot = $this->redisManager->fetchSlot($command->getReadUid());
			
			if ($readSlot && $readSlot instanceof ReadSlot) {
			    $readsSlot->setSecret($command->getSecret());
                            $this->redisManager->persistSlot($readSlot);
			    $this->redisManager->deleteSlot($writeSlot);
			}
		}
	}

	private function handleReadSecretCommand(ReadSecretCommand $command)
	{
		$readSlot = $this->redisManager->fetchSlot($command->getReadUid());

		if ($readSlot && $readSlot instanceof ReadSlot) {
			$secret = $readSlot->revealSecret($command->getPassword());
			if ($secret) {
				$this->redisManager->deleteSlot($readSlot);	
			}
			
			return $secret;
		}

		return null;
	}
}
