<?php

namespace Marvic\HTTP;

use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;

/**
 * An Abstract HTTP Message Representation.
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
abstract class Message {
	/** @var string */
	public readonly string $version;
	
	/** @var Marvic\HTTP\Header\Collection */
	public readonly Headers $headers;
	
	/** @var Marvic\HTTP\Cookie\Collection */
	public readonly Cookies $cookies;
	
	/** @var mixed */
	protected string $body;

	/** @var string */
	protected string $type = '';

	/** @var string */
	protected string $charset = '';

	/** @var integer */
	protected int $length = 0;


	public function __construct(string $version, Headers $headers,
		Cookies $cookies, string $body = ''
	) {
		$this->version = $version;
		$this->headers = $headers;
		$this->cookies = $cookies;
		$this->body    = $body;

		if ( $headers->has('Content-Type') ) {
			$parts = explode(';', $headers->get('Content-Type'));
			$this->type = trim($parts[0]) ?? '';
			
			if ( isset($parts[1]) )
				$this->charset = str_replace('charset=', '', trim($found[1])) ?? '';
			
			$this->length = strlen($this->body);
		}
	}

	abstract public function __toString(): string;

	final public function read(): string {
		return $this->body;
	}
}