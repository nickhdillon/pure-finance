<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Account;
use App\Models\Transaction;
use App\Enums\TransactionType;

class CreateTransfer
{
	public function handle(Account $transfer_to_account, Transaction $transaction): void
	{
		$transfer_to_account->transactions()->updateOrCreate(
			[
				'transfer_to' => $transaction->transfer_to,
				'parent_id' => $transaction->id,
			],
			[
				'category_id' => $transaction->category_id,
				'type' => TransactionType::CREDIT,
				'transfer_to' => $transaction->transfer_to,
				'amount' => $transaction->amount,
				'payee' => Account::find($transaction->account_id)->name,
				'date' => $transaction->date,
				'notes' => $transaction->notes ?? null,
				'attachments' => $transaction->attachments ?? null,
				'status' => $transaction->status,
				'is_recurring' => $transaction->is_recurring,
				'frequency' => $transaction->frequency ?? null,
				'recurring_end' => $transaction->recurring_end ?? null,
				'parent_id' => $transaction->id,
			]
		)->recalculateAccountBalance();
	}
}
