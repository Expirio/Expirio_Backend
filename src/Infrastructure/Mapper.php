<?php

namespace App\Infrastructure;

use App\Domain\ReadSlot\ReadSlot;
use App\Domain\WriteSlot\WriteSlot;

class Mapper
{
	public static function toDomain(string $guid, array $persistenceData)
	{
		if (isset($persistenceData['password'])) {
			$secret = !empty($persistenceData['secret']) ? $persistenceData['secret'] :  null;
			return new ReadSlot($guid, $persistenceData['password'], $secret, $persistenceData['attempts']);
		}

		if (isset($persistenceData['read_slot'])) {
			$secret = !empty($persistenceData['secret']) ? $persistenceData['secret'] :  null;
			return new WriteSlot($guid, $persistenceData['read_slot'], $secret);
		}
	}

	public static function toPersistence($slotDomain)
	{
		if ($slotDomain instanceof ReadSlot) {
			return [
				'password' => $slotDomain->getPassword(),
				'secret' => $slotDomain->getSecret(),
				'attempts' => $slotDomain->getAmountOfAttempts()
			];
		}

		if ($slotDomain instanceof WriteSlot) {
			return ['read_slot' => $slotDomain->getReadUi()];
		}
	}
}