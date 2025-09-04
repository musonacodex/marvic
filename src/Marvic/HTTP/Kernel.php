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
 *
 * @package Marvic\HTTP
 */
final class Kernel {
	/**
	 * The HTTP Message Factory.
	 * 
	 * @var Marvic\HTTP\Message\Factory
	 */
	private readonly MessageFactory $factory;

	/**
	 * The HTTP Message Transport.
	 * 
	 * @var Marvic\HTTP\Message|Transport
	 */
	private readonly MessageTransport $transport;

	/**
	 * The Instance Constructor Method.
	 */
	public function __construct() {
		$this->factory   = new MessageFactory();
		$this->transport = new MessageTransport();
	}

	/**
	 * Create a new immutable HTTP request.
	 * 
	 * @param  Marvic\Application $app
	 * @param  string             $method
	 * @param  string             $url
	 * @param  array              $options
	 * @return Marvic\HTTP\Message\Request
	 */
	public function newRequest(?Application $app, string $method,
		string $url, array $options = []): Request
	{
		return $this->factory->newRequest($app, $method, $url, $options);
	}

	/**
	 * Create a new immutable HTTP request from global functions
	 * .
	 * @param  Marvic\Application          $app
	 * @return Marvic\HTTP\Message\Request
	 */
	public function captureRequest(Application $app): Request {
		return $this->factory->captureRequest($app);
	}

	/**
	 * Create a new HTTP response.
	 * 
	 * @param  Marvic\HTTP\Message\Request  $request
	 * @param  int|integer                  $status
	 * @return Marvic\HTTP\Message\Response
	 */
	public function newResponse(Request $request, int $status = 200): Response {
		return $this->factory->newResponse($request, $status);
	}

	/**
	 * Send an HTTP Message.
	 * 
	 * @param  Marvic\HTTP\Message $message
	 * @return mixed
	 */
	public function send(Message $message): mixed {
		if ($message instanceof Request)
			return $this->transport->sendRequest($message);

		if ($message instanceof Response)
			return $this->transport->sendResponse($message);
	}
}