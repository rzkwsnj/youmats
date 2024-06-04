<?php

namespace App\Mail;

use App\Models\Subscribe;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoticeExpirySubscribe extends Mailable
{
    use Queueable, SerializesModels;

    public Vendor $vendor;
    public Subscribe $subscribe;
    public $diff;

    /**
     * NoticeExpirySubscribe constructor.
     * @param Vendor $vendor
     * @param Subscribe $subscribe
     * @param $diff
     */
    public function __construct(Vendor $vendor, Subscribe $subscribe, $diff)
    {
        $this->vendor = $vendor;
        $this->subscribe = $subscribe;
        $this->diff = $diff;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.notice_expiry_subscribe');
    }
}
