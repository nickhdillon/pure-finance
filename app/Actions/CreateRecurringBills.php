<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Bill;
use Carbon\CarbonInterval;
use App\Enums\RecurringFrequency;

use function Illuminate\Support\defer;

class CreateRecurringBills
{
	public function handle(Bill $bill): void
	{
		defer(function () use ($bill): void {
			$this->createRecurringBills($bill);
		});
	}

	private function createRecurringBills(Bill $bill): void
	{
		$interval = $this->frequencyToInterval($bill->frequency);
		$date = $bill->date->copy();
		$end_of_year = now('America/Chicago')->copy()->endOfYear();

		while (true) {
			$date = $date->copy()->add($interval);

			if ($date->greaterThan($end_of_year)) break;

			Bill::create([
				'account_id' => $bill->account_id,
				'user_id' => $bill->user_id,
				'parent_id' => $bill->id,
				'name' => $bill->name,
				'category_id' => $bill->category_id,
				'amount' => $bill->amount,
				'date' => $date,
				'frequency' => $bill->frequency,
				'notes' => $bill->notes,
				'first_alert' => $bill->first_alert,
				'first_alert_time' => $bill->first_alert_time,
				'second_alert' => $bill->second_alert,
				'second_alert_time' => $bill->second_alert_time,
			]);
		}
	}

	private function frequencyToInterval(RecurringFrequency $frequency): CarbonInterval
	{
		return match ($frequency->value) {
			'month' => CarbonInterval::month(),
			'quarter' => CarbonInterval::months(3),
			'semi_annual' => CarbonInterval::months(6),
			'year' => CarbonInterval::year()
		};
	}
}
