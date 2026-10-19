<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusChanged extends Notification
{
    public function __construct(public ServiceRequest $serviceRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on ' . $this->serviceRequest->reference_no)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your request "' . $this->serviceRequest->subject . '" is now: ' . $this->serviceRequest->statusLabel() . '.')
            ->action('View request', route('requests.show', $this->serviceRequest))
            ->line('Thank you for using StudentCare.');
    }
}
