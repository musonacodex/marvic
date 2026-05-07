<?php

namespace Marvic\View;

use Throwable;
use InvalidArgumentException;

final class PhpTemplateRenderer {
	public function render(string $path, array $data = []): string {
		if (! file_exists($path) )
			throw new InvalidArgumentException("File does not found: '$path'");

		if (! is_readable($path) )
			throw new InvalidArgumentException("File is not readable: '$path'");

		$render = function(string $__path, array $__data = []): string {
			extract($__data, EXTR_SKIP);

			ob_start();
			try {
				include $__path;
				return ob_get_clean();
			} catch (Throwable $e) {
				ob_end_clean();
				throw $e;
			}
		};
		return $render($path, $data);
	}
}