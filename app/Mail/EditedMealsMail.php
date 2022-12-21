<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EditedMealsMail extends Mailable
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
                    ->view('common.editedMealsMail')
                    ->subject('Edited Meals')
                    ->with('data',$this->data);
                    // return $this->subject($this->subject)->view('demomail')->with('data',$this->data);
    }
}
