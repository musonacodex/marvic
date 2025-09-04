<?php

namespace Marvic\HTTP;

use Marvic\HTTP\Header\Collection as Headers;
use Marvic\HTTP\Cookie\Collection as Cookies;

/**
 * An Abstract HTTP Message Representation.
 * 
 * @package Marvic\HTTP
 */
abstract class Message {
	/**
	 * The used HTTP Version.
	 * 
	 * @var string
	 */
	public readonly string $version;
	
	/**
	 * The HTTP Message Headers.
	 * 
	 * @var Marvic\HTTP\Header\Collection
	 */
	public readonly Headers $headers;
	
	/**
	 * The HTTP Message Cookies.
	 * 
	 * @var Marvic\HTTP\Cookie\Collection
	 */
	public readonly Cookies $cookies;
	
	/**
	 * The HTTP Message Body.
	 * 
	 * @var mixed
	 */
	protected string $body;

	/**
	 * The value of Content-Type Header.
	 * 
	 * @var string
	 */
	protected string $type = '';

	/**
	 * The charset parameter value of Content-Type Header.
	 * 
	 * @var string
	 */
	protected string $charset = '';

	/** 
	 * The value of Content-Length Header.
	 * 
	 * @var integer
	 */
	protected int $length = 0;

	/**
	 * The Instance Constructor Method.
	 * 
	 * @param string                        $version
	 * @param Marvic\HTTP\Header\Collection $headers
	 * @param Marvic\HTTP\Cookie\Collection $cookies
	 * @param string                        $body
	 */
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

	/**
	 * Must return the raw representation of HTTP message.
	 * 
	 * @return string
	 */
	abstract public function __toString(): string;

	/**
	 * Read the HTTP message body.
	 * 
	 * @return string
	 */
	final public function read(): string {
		return $this->body;
	}
}