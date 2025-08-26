<?php

namespace Marvic\HTTP;

/**
 * An HTTP Session Manager
 * 
 * @package Marvic\HTTP
 * @version 1.0.0
 */
final class Session {
	/**
	 * @var string The session ID.
	 */
	private string $id;

	/**
	 * @var string The session cookie name.
	 */
	private string $name;

	/**
	 * @var string The session secret.
	 */
	private string $secret;

	/**
	 * @var array<string, mixed> The session data.
	 */
	private array $data;

	/**
	 * @var array<string, string> The session cookie options.
	 */
	private array $options;

	/**
	 * @var mixed The customed store (Redis, database, etc)
	 */
	private mixed $store;
	
	/**
	 * @var bool
	 */
	private bool $destroy = false;

	/**
	 * The Constructor Instance Method
	 */
	public function __construct(string $name = 'SESSID', array $options = [],
		mixed $store = null
	) {
		$this->name    = $name;
		$this->store   = $store;
		$this->options = array_merge([
			'lifetime' => time() + 3600,
			'path'     => '/',
			'domain'   => '',
			'secure'   => false,
			'httpOnly' => true,
			'sameSite' => 'Lax',
		], $options);
	}

	private function started(): bool {
		return session_status() !== PHP_SESSION_NONE;
	}
	
	private function start(): void {
		if ( $this->started() ) return;
		
		session_set_cookie_params($this->options);
		session_name($this->name);
		session_start();
		
		$this->id = session_id();
	}

	public function get(string $key, mixed $default = null): mixed {
		$this->start();
		return $_SESSION[$key] ?? $default;
	}

	public function set(string $key, mixed $value): void {
		$this->start();
		$_SESSION[$key] = $value;
	}

	public function has(string $key): bool {
		$this->start();
		return array_key_exists($key, $_SESSION);
	}
	
	public function remove(string $key): void {
		$this->start();
		unset($_SESSION[$key]);
	}

	public function regenerate(): void {
		$this->start();
		session_regenerate_id();
		$this->id = session_id();
	}

	public function destroy(): void {
		$this->start();
		$this->destroy = true;
	}

	public function reset(): void {
		$this->start();
		$_SESSION = [];
	}	

	public function send(): void {
		if (! $this->started() ) return;

		if ( $this->destroy ) {
			$this->reset();
			session_destroy();
			setcookie($this->name, '', time()-3600, $this->options['path']);
		}
		session_write_close();
	}
}