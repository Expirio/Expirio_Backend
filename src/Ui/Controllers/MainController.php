<?php
namespace App\Ui\Controllers;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Infrastructure\SlotsManagerRedis;
use Predis\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
	private $handler;

	public function __construct()
	{
		$this->handler = new CommandHandler(
			new SlotsManagerRedis(
				new Client([
					"host" => "localhost",
					"port" => 6379
				])
			)
		);
	}

	/**
	 * @Route("/create/{password}/{expirationPeriod}", name="create")
	 * @example
	 *         /create/mypass/PT3H
	 *         /create/mypass/P3D
	 *         /create/mypass/P3DT3S
	 */
	public function create(String $password, String $expirationPeriod)
	{
		$slots = $this->handler->handle(
			new CreatePairSlotsCommand(
				Uuid::uuid4()->toString(),
				Uuid::uuid4()->toString(),
				$password,
				$expirationPeriod
			)
		);

		return new Response(
			json_encode([
				'read_url' => $slots->getReaduid(),
				'write_url' => $slots->getWriteuid(),
			])
		);
	}

	/**
	 * @Route("/write", name="write")
	 */
	public function write()
	{
		return new Response(
			'<html><body>write</body></html>'
		);
	}

	/**
	 * @Route("/read", name="read")
	 */
	public function read()
	{
		return new Response(
			'<html><body>Read</body></html>'
		);
	}
}