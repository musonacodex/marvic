<?php

namespace Marvic\Http\Message;

use Exception;
use Marvic\Http\Message;
use Marvic\Http\Message\Response;

final class Transport {
	private static function sendResponse(Response $response): void {
		$headers = explode("\r\n\r\n", "$response");
		$headers = explode("\r\n", $headers[0]);
		foreach ($headers as $header) header($header);

		if ($response->status === 206 && $response->has('Content-Range')) {
			$rangeStr = $response->get('Content-Range');
			preg_match('/bytes (\d+)-(\d+)\/(\d+)/', $rangeStr, $found);

			$start = intval( $found[1] );
			$end   = intval( $found[2] );
			$size  = intval( $found[3] );

			$_file = fopen($response->read(), 'rb');
			fseek($_file, $start);

			while ( !feof($_file) && ftell($_file) <= $end )
				fread($_file, 1024 * 8); flush();
			fclose($_file);
	
			return;
		}
		if ( $response->has('Content-Disposition') ) {
			readfile($response->read());
			return;
		}

		echo $response->read();
	}

	public static function send(Message $message): void {
		if ($message instanceof Response) self::sendResponse($message);
	}
}
