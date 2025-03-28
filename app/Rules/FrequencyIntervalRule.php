<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use App\Enums\RecurringFrequency;
use Illuminate\Contracts\Validation\ValidationRule;

class FrequencyIntervalRule implements ValidationRule
{
    public function __construct(
        protected Carbon $start_date,
        protected ?Carbon $end_date,
        protected RecurringFrequency $frequency
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($this->start_date);

        $end = Carbon::parse($this->end_date);

        if ($end->lte($start)) {
            $fail('The end date must be after the start date.');

            return;
        }

        match ($this->frequency) {
            RecurringFrequency::MONTHLY => $start->addMonth()->isSameDay($end),
            RecurringFrequency::YEARLY => $start->addYear()->isSameDay($end),
        };
    }
}
