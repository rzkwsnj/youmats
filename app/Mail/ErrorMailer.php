<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ErrorMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $error_details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($error_details)
    {
        $this->error_details = $error_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $data["level"] = $this->error_details["level"];
        $data["message"] =  $this->error_details["message"];
        $data["url"] = $this->error_details["remote_addr"];
        $data["context"] = json_encode($this->error_details["context"]);
        $data["date"] = date("Y-m-d H:i:s");

        return $this->subject('Error : ' . $data["level"] . ' - ' . Str::limit($data["message"] , 50))
                    ->markdown('mails.ErrorMail')
                    ->with($data);
    }
}
