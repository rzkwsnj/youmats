<?php

namespace App\Console\Commands;

use App\Models\Subscribe;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NoticeExpirySubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscribes = Subscribe::whereDate('expiry_date', '>=', now())->get();
        $now = Carbon::now();

        foreach ($subscribes as $subscribe) {
            $subscribe_expiry_date = Carbon::parse($subscribe->expiry_date);
            $diff = $subscribe_expiry_date->diffInDays($now);
            $vendor = Vendor::where('vendor_id', $subscribe->vendor_id)->first();
            if($diff < 3) {
                Mail::to($vendor)->send(new \App\Mail\NoticeExpirySubscribe($vendor, $subscribe, $diff));
            }
        }
    }
}
