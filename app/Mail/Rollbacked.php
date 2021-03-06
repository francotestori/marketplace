<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Rollbacked extends Mailable
{
    use Queueable, SerializesModels;

    private $reason;
    private $transactions;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reason = null, $transactions = null)
    {
        $this->reason = $reason;
        $this->transactions = $transactions;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.rollback')
            ->with('transactions', $this->transactions)
            ->with('reason', $this->reason)
            ->attach('');
    }
}
