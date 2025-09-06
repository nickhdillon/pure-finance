<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Bill;
use App\Enums\BillAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;

class BillAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Bill $bill, public BillAlert $alert_type) {}

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(): VonageMessage
    {
        $message = match ($this->alert_type) {
            BillAlert::DAY_OF => 'today',
            BillAlert::ONE_DAY_BEFORE => 'tomorrow',
            BillAlert::TWO_DAYS_BEFORE => 'in two days',
            BillAlert::ONE_WEEK_BEFORE => 'in one week',
        };

        $route = route('bill-calendar', $this->bill);

        return (new VonageMessage)
            ->content('Pure Finance - Bill Reminder: Your ' . trim($this->bill->name) . " bill is due {$message}. {$route}. See details.");
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['vonage'];
    }
}
