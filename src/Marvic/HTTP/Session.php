<?php

namespace Marvic\HTTP;

/**
 * HTTP Session
 * 
 * @package Marvic\HTTP
 */
final class Session extends Cookie {
	/**
	 * @var string The session ID.
	 */
	private ?string $id = null;

	/**
	 * @var array<string, mixed> The session data.
	 */
	private array $data = [];
	
	/**
	 * @var bool
	 */
	private bool $destroy = false;

	/**
	 * The Constructor Instance Method
	 */
	public function __construct(string $name = 'SESSID', array $options = []) {
		parent::__construct($name, '', $options);
		session_name($this->name);
		session_set_cookie_params([
			'path'     => $this->path      ?? '/',
			'domain'   => $this->domain    ?? '',
			'secure'   => $this->secure    ?? false,
			'httpOnly' => $this->httpOnly  ?? true,
			'sameSite' => $this->sameSite  ?? 'Lax',
			'lifetime' => $this->expiresAt ?? time() + 3600,
		]);
	}

	/**
	 * Start the session.
	 */
	private function start(): void {
		if (session_status() !== PHP_SESSION_NONE) return;
		session_start();
		$this->id   = session_id();
		$this->data = $_SESSION;
	}

	/**
	 * Generate a new session ID.
	 */
	public function regenerate(): void {
		$this->start();
		session_regenerate_id();
		$this->id = session_id();
	}

	/**
	 * Get a value of a session variable.
	 * 
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get(string $key, mixed $default = null): mixed {
		$this->start(); return $this->data[$key] ?? $default;
	}

	/**
	 * Set a value of a session variable.
	 * 
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set(string $key, mixed $value): void {
		$this->start(); $this->data[$key] = $value;
	}

	/**
	 * Check if a session variable exists.
	 * 
	 * @param  string  $key
	 * @return boolean
	 */
	public function has(string $key): bool {
		$this->start(); return array_key_exists($key, $this->data);
	}

	/**
	 * Remove a session variable.
	 * 
	 * @param string $key
	 */
	public function delete(string $key): void {
		$this->start(); unset($this->data[$key]);
	}

	/**
	 * Destroy the session.
	 */
	public function destroy(): void {
		$this->forget = true;
	}

	/**
	 * Reset all session data.
	 */
	public function reset(): void {
		$this->start(); $this->data = [];
	}	

	/**
	 * Send the sesstion cookie to client.
	 */
	public function send(): void {
		if (session_status() === PHP_SESSION_NONE) return;
		if ($this->data !== $_SESSION) $_SESSION = $this->data;
		if ( $this->forget ) session_destroy();
		session_write_close();
	}
}