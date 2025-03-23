<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class S3Service
{
	public static function getS3Path(string $file_name): string
	{
		return Storage::disk('s3')->url("files/{$file_name}");
	}
}
