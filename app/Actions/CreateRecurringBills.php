<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\RecurringFrequency;
use App\Models\Bill;
use Carbon\Carbon;
use Carbon\CarbonInterval;

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

        $recurs_at_end_of_month = in_array($bill->frequency, [
            RecurringFrequency::MONTHLY,
            RecurringFrequency::QUARTERLY,
            RecurringFrequency::SEMI_ANNUALLY,
            RecurringFrequency::YEARLY,
        ], strict: true)
            && $bill->date->isLastOfMonth();

        $date = $bill->date->copy();

        $end_of_year = now('America/Chicago')->endOfYear();

        $date = $this->nextDate($date, $bill->frequency, $interval, $recurs_at_end_of_month);

        while ($date->toDateString() <= $end_of_year->toDateString()) {
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

            $date = $this->nextDate($date, $bill->frequency, $interval, $recurs_at_end_of_month);
        }
    }

    private function nextDate(
        Carbon $date,
        RecurringFrequency $frequency,
        CarbonInterval $interval,
        bool $recurs_at_end_of_month,
    ): Carbon {
        if ($recurs_at_end_of_month) {
            $months = match ($frequency) {
                RecurringFrequency::MONTHLY => 1,
                RecurringFrequency::QUARTERLY => 3,
                RecurringFrequency::SEMI_ANNUALLY => 6,
                RecurringFrequency::YEARLY => 12,
                default => 0,
            };

            return $date->copy()->addMonthsNoOverflow($months)->endOfMonth();
        }

        return $date->copy()->add($interval);
    }

    private function frequencyToInterval(RecurringFrequency $frequency): CarbonInterval
    {
        return match ($frequency->value) {
            'bi_weekly' => CarbonInterval::weeks(2),
            'month' => CarbonInterval::month(),
            'quarter' => CarbonInterval::months(3),
            'semi_annual' => CarbonInterval::months(6),
            'year' => CarbonInterval::year()
        };
    }
}
