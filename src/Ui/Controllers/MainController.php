<?php
namespace App\Ui\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
	/**
	 * @Route("/create", name="create")
	 */
	public function create()
	{
		return new Response(
			'<html><body>Create</body></html>'
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