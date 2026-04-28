<?php

namespace Marvic\Http\Message\Request;

use RuntimeException;

final class File {
	public readonly string $name;
	public readonly string $type;
	public readonly string $origin;
	public readonly int    $size;
	public readonly int    $error;
	public readonly string $message;
	
	public function __construct(string $name, string $type,
		string $origin, int $error, int $size)
	{
		$this->name    = $name;
		$this->type    = $type;
		$this->origin  = $origin;
		$this->size    = $size;
		$this->error   = $error;
		$this->message = match($this->error) {
			UPLOAD_ERR_OK         => 'No error',
			UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize directive',
			UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE directive',
			UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
			UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
			UPLOAD_ERR_EXTENSION  => 'File upload stopped by extension',
			default               => 'Unknown upload error'
		};
	}

	public function valid(): bool {
		return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->origin);
	}

	public function getStream() {
		if (!$this->isValid()) {
			throw new RuntimeException('Cannot get stream from invalid uploaded file');
		}
		$resource = fopen($this->tmpName, 'rb');
		if ($resource === false) {
			throw new RuntimeException('Could not open stream for uploaded file');
		}
		return $resource;
	}
		
	public function getContents(): string
	{
		if (!$this->isValid()) {
			throw new RuntimeException('Cannot read invalid uploaded file');
		}
		$contents = file_get_contents($this->tmpName);
		if ($contents === false) {
			throw new RuntimeException('Could not read uploaded file contents');
		}
		return $contents;
	}
	
	public function store(string $targetPath): void {
		if (!$this->isValid())
			throw new RuntimeException('Cannot move invalid uploaded file');
		
		$targetDirectory = dirname($targetPath);
		if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true))
			throw new RuntimeException('Could not create target directory');
		
		if (!move_uploaded_file($this->tmpName, $targetPath))
			throw new RuntimeException('Could not move uploaded file');
	}

	public static function fromGlobals(): array {
		$result = $_FILES;
		return $result;
	}
}
