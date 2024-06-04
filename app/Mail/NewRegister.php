<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRegister extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    /**
     * NewRegister constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['name'] = $this->user->name;
        if (class_basename($this->user) == 'Vendor') {
            $data['type'] = 'Vendor';
            $data['url'] = url('/management/resources/vendors/' . $this->user->id);
        } elseif (class_basename($this->user) == 'User') {
            $data['url'] = url('/management/resources/users/' . $this->user->id);
            if ($this->user->type == 'individual') {
                $data['type'] = 'User';
            } elseif ($this->user->type == 'company') {
                $data['type'] = 'Company';
            }
        }

        return $this->markdown('mails.newRegister')->with($data);
    }
}
