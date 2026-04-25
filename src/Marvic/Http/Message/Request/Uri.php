<?php

namespace Marvic\Http\Message\Request;

use InvalidArgumentException;

final class Uri {
	public readonly string $scheme;
	public readonly string $username;
	public readonly string $password;
	public readonly string $host;
	public readonly ?int   $port;
	public readonly string $path;
	public readonly string $query;
	public readonly string $fragment;

	public readonly string $authority;
	public readonly string $user;
	public readonly string $fullpath;
	public readonly string $baseurl;
	public readonly string $fullurl;

	private readonly array $queryParams;

	public function __construct(string $url) {
		$parsed = parse_url($url);
		if ($parsed === false) {
			throw new InvalidArgumentException("Invalid URL: {$url}");
		}
		if ( isset($parsed['port']) ) 
			$parsed['port'] = (int) $parsed['port'];

		$this->scheme   = $parsed['scheme']   ?? '';
		$this->username = $parsed['user']     ?? '';
		$this->password = $parsed['pass']     ?? '';
		$this->host     = $parsed['host']     ?? '';
		$this->port     = $parsed['port']     ?? null;
		$this->path     = $parsed['path']     ?? '';
		$this->query    = $parsed['query']    ?? '';
		$this->fragment = $parsed['fragment'] ?? '';

		$this->user      = $this->buildUserInfo();
		$this->authority = $this->buildAuthority();
		$this->fullpath  = $this->buildFullPath();
		$this->baseurl   = $this->buildBaseUrl();
		$this->fullurl   = $this->buildFullUrl();

		parse_str($this->query, $queryParams);
		$this->queryParams = $queryParams;
	}

	private function buildUserInfo(): string {
		$userInfo = "{$this->username}";
		if ($this->password !== null)
			$userInfo .= ":{$this->password}";
		return $userInfo;
	}

	private function buildAuthority(): string {
		if ( empty($this->host) ) return '';		
		$authority  = $this->user ? "{$this->user}@" : '';
		$authority .= $this->host . (is_null($this->port) ? '' : ":{$this->port}");
		return $authority;
	}

	private function buildFullPath(): string {
		$fullpath = $this->path;
		if (! empty($this->query) )    $fullpath .= "?{$this->query}";
		if (! empty($this->fragment) ) $fullpath .= "#{$this->fragment}";
		return $fullpath;
	}

	private function buildBaseUrl(): string {
		$base = '';
		if (! empty($this->scheme)    ) $base  = "{$this->scheme}://";
		if (! empty($this->authority) ) $base .= $this->authority;
		return $base;
	}

	private function buildFullUrl(): string {
		return $this->baseurl . $this->fullpath;
	}

	public function allQueries(): array {
		return $this->queryParams;
	}

	public function query(string $key, ?string $default = null): ?string {
		return $this->queryParams[$key] ?? $default;
	}

	public static function fromGlobals(): self {
		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
		$host   = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '0.0.0.0';
		$url    = $_SERVER['REQUEST_URI'] ?? '/';
		return new self("$scheme://" . $host . $url);
	}
}