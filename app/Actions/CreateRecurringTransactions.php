<?php

declare(strict_types=1);

namespace App\Actions;

use Carbon\CarbonInterval;
use App\Models\Transaction;
use App\Enums\RecurringFrequency;
use function Illuminate\Support\defer;

class CreateRecurringTransactions
{
	public function handle(Transaction $transaction): void
	{
		if ($transaction->is_recurring) {
			defer(function () use ($transaction): void {
				$this->createRecurringTransactions($transaction);
			});
		}
	}

	private function createRecurringTransactions(Transaction $transaction): void
	{
		$start_date = $transaction->date;

		$start_date->add($this->frequencyToInterval($transaction->frequency));

		while (
			$start_date->lte($transaction->recurring_end ?? now()->timezone('America/Chicago')->addYear())
		) {
			Transaction::create([
				'account_id' => $transaction->account->id,
				'payee' => $transaction->payee,
				'type' => $transaction->type,
				'amount' => $transaction->amount,
				'category_id' => $transaction->category_id,
				'date' => $start_date->copy(),
				'tags' => $transaction->tags,
				'notes' => $transaction->notes,
				'status' => 0,
				'is_recurring' => false,
				'parent_id' => $transaction->id,
			]);

			$start_date->add($this->frequencyToInterval($transaction->frequency));
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
