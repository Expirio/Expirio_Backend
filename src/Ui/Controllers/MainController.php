<?php
namespace App\Ui\Controllers;

use App\Application\Service\CommandHandler;
use App\Application\Service\CreatePairSlotsCommand;
use App\Application\Service\ReadSecretQuery;
use App\Application\Service\WriteSecretCommand;
use App\Infrastructure\SlotsManagerRedis;
use Predis\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
	 * @Route("/create/{password}/{expirationPeriod}", name="create", methods={"GET"})
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

		return new JsonResponse([
			'read_url' => '/' . $slots->getReaduid(),
			'write_url' => '/' . $slots->getWriteuid(),
		]);
	}

	/**
	 * @Route("/write/{writeuid}", name="write", methods={"PUT"})
	 */
	public function write($writeuid, Request $request)
	{
		$secret = $request->getContent();

		$this->handler->handle(
			new WriteSecretCommand($writeuid, $secret)
		);

		return new Response();
	}

	/**
	 * @Route("/read/{readuid}/{password}", name="read", methods={"GET"})
	 */
	public function read($readuid, $password)
	{
		$secret = $this->handler->handle(
			new ReadSecretQuery($readuid, $password)
		);

		return new Response($secret);
	}
}