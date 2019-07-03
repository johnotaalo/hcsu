<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class FocalPointPassword extends ResetPasswordNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $type;
    protected $focalPoint;
    public $token;

    public function __construct($focalPoint, $token, $type="new")
    {
        $this->type = $type;
        $this->focalPoint = $focalPoint;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = ($this->type == "new") ? "Host Country: Focal Point Account Created!" : "Host Country: Password Reset Link";
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting("Dear: {$this->focalPoint->full_name}")
                    ->line('Welcome to Host Country Services Unit')
                    ->line("You have been added as a focal point for your organization: {$this->focalPoint->agency->AGENCY_NAME}. To be able to access the system, please click on the link below to set your password")
                    ->line("Your username is: {$this->focalPoint->USERNAME}")
                    ->action('Set My Password', route('focalpoint-reset-password', ['token' => $this->token]))
                    ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
