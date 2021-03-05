<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExcelDump extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $user;
    protected $attachment;

    public function __construct($user, $attachment)
    {
        $this->user = $user;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailer = $this
            ->from(env('MAIL_FROM_EMAIL'))
            ->subject("Host Country Data Dump")
            ->view('emails.send_excel')
            ->with([
                'name'  =>  $this->user->name
            ])
            ->attachData($this->attachment, "HCSU Data.xlsx", [
                'mime'  =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);

        return $mailer;
    }
}
