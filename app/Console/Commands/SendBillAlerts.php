<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\User;
use App\Enums\BillAlert;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\BillAlertNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SendBillAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-bill-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bill alerts';

    public function __construct(public string $timezone = 'America/Chicago')
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now($this->timezone);

        $one_hour_ago = $now->copy()->subHour();

        User::query()
            ->whereNotNull('phone_numbers')
            ->with(['bills' => function (HasMany $query) use ($now): void {
                $query->whereDate('date', '>=', $now->toDateString())
                    ->where(function (Builder $q): void {
                        $q->where(function (Builder $sub): void {
                            $sub->whereNotNull('first_alert')
                                ->where('first_alert_sent', false);
                        })->orWhere(function (Builder $sub): void {
                            $sub->whereNotNull('second_alert')
                                ->where('second_alert_sent', false);
                        });
                    });
            }])
            ->get()
            ->filter(fn(User $user): bool => $user->bills->isNotEmpty())
            ->each(function (User $user) use ($now, $one_hour_ago): void {
                $user->bills->each(function (Bill $bill) use ($now, $one_hour_ago): void {
                    $date = Carbon::createFromFormat('Y-m-d', $bill->date->format('Y-m-d'), $this->timezone)->startOfDay();

                    $send_first = false;
                    $send_second = false;

                    if (
                        !$bill->first_alert_sent &&
                        $this->shouldSendAlert($date, $bill->first_alert, $bill->first_alert_time, $now, $one_hour_ago)
                    ) {
                        $send_first = true;
                    }

                    if (
                        !$bill->second_alert_sent &&
                        $this->shouldSendAlert($date, $bill->second_alert, $bill->second_alert_time, $now, $one_hour_ago)
                    ) {
                        $send_second = true;
                    }

                    if ($send_first) {
                        $this->sendAlert($bill, $bill->first_alert);
                        $bill->update(['first_alert_sent' => true]);
                    }

                    if ($send_second) {
                        $this->sendAlert($bill, $bill->second_alert);
                        $bill->update(['second_alert_sent' => true]);
                    }
                });
            });
    }

    protected function shouldSendAlert(
        Carbon $date,
        ?BillAlert $alert_type,
        ?string $alert_time,
        Carbon $now,
        Carbon $one_hour_ago
    ): bool {
        if (!$alert_type || !$alert_time) return false;

        $alert_date = match ($alert_type) {
            BillAlert::DAY_OF => $date->copy(),
            BillAlert::ONE_DAY_BEFORE => $date->copy()->subDay(),
            BillAlert::TWO_DAYS_BEFORE => $date->copy()->subDays(2),
            BillAlert::ONE_WEEK_BEFORE => $date->copy()->subWeek(),
            default => null,
        };

        if (!$alert_date) return false;

        $parsed_time = Carbon::parse($alert_time);
        $alert_date->setTimeFrom($parsed_time);

        return $alert_date->between($one_hour_ago, $now);
    }

    protected function sendAlert(Bill $bill, BillAlert $alert_type): void
    {
        foreach ($bill->user->phone_numbers as $phone) {
            Notification::route('vonage', "+1{$phone['value']}")
                ->notify(new BillAlertNotification($bill, $alert_type));
        }
    }
}
