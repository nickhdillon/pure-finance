<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\BillAlert;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\WithCast;

class BillAlertData extends Data
{
	public function __construct(
		#[WithCast(EnumCast::class, BillAlert::class)]
		public BillAlert $alert,
		public string $time,
	) {}
}
