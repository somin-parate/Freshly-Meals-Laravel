<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        // echo '<pre>';print_r($this->data);exit;
        // $this->subject = $data->subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("freshlymeals123@gmail.com")
                    ->view('common.resubscriptionMail')
                    ->subject('Meal Selection')
                    ->with('data',$this->data);
                    // return $this->subject($this->subject)->view('demomail')->with('data',$this->data);
    }
}
