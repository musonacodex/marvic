<?php

namespace Marvic\HTTP;

use Marvic\Application;
use Marvic\HTTP\Message;
use Marvic\HTTP\Message\Request;
use Marvic\HTTP\Message\Response;
use Marvic\HTTP\Message\Factory as MessageFactory;
use Marvic\HTTP\Message\Transport as MessageTransport;

/**
 * The HTTP Kernel.
 *
 * This class manage and control the HTTP request and response,
 * including headers, cookies, sessions and uploaded files.
 */
final class Kernel {
	/**
	 * @var MessageFactory The HTTP Message Factory.
	 */
	private readonly MessageFactory $factory;

	/**
	 * @var MessageTransport The HTTP Message Transport.
	 */
	private readonly MessageTransport $transport;

	public function __construct() {
		$this->factory   = new MessageFactory();
		$this->transport = new MessageTransport();
	}

	public function newRequest(?Application $app, string $method,
		string $url, array $options = []): Request
	{
		return $this->factory->newRequest($app, $method, $url, $options);
	}

	public function captureRequest(Application $app): Request {
		return $this->factory->captureRequest($app);
	}

	public function newResponse(Request $request, int $status = 200): Response {
		return $this->factory->newResponse($request, $status);
	}

	public function send(Message $message) {
		if ($message instanceof Request)
			return $this->transport->sendRequest($message);

		if ($message instanceof Response)
			return $this->transport->sendResponse($message);
	}
}