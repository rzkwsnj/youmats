<?php

namespace App\Logging;

use App\Mail\ErrorMailer;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Jaybizzle\CrawlerDetect\CrawlerDetect;


class MySQLLoggingHandler extends AbstractProcessingHandler
{

    protected $table;
    protected $connection;

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->table = 'error_logs';
        parent::__construct($level, $bubble);
    }

    protected function write($record): void
    {
        $CrawlerDetect = new CrawlerDetect;
        if (!$CrawlerDetect->isCrawler(Request::userAgent())) {

            $error_existance = ErrorLog::where('level', '=', $record['level'])
                ->where('level_name', '=', $record['level_name'])
                ->where('channel', '=', $record['channel'])
                ->where('remote_addr', '=', url()->current())
                ->orderBy('updated_at', 'DESC')->limit(1);


            if ($error_existance->count() > 0) {

                $new_counter = $error_existance->value('counter') + 1;
                DB::table($this->table)->where('level', '=', $record['level'])
                    ->where('level_name', '=', $record['level_name'])
                    ->where('channel', '=', $record['channel'])
                    ->where('remote_addr', '=', url()->current())
                    ->update(['counter' => $new_counter, 'updated_at' => date("Y-m-d H:i:s")]);

                if ($error_existance->value('counter') > 10) {
                    $record["remote_addr"] = url()->current();
                    Mail::to([
                        //'info@youmats.com',
                        //'sameh@youmats.com',
                        'zakaria@youmats.com'
                    ])->send(new ErrorMailer($record));
                }
            } else {

                $data = array(
                    'message'       => $record['message'],
                    'context'       => json_encode($record['context']),
                    'level'         => $record['level'],
                    'level_name'    => $record['level_name'],
                    'channel'       => $record['channel'],
                    'extra'         => json_encode($record['extra']),
                    'counter'       => '1',
                    'remote_addr'   => url()->current(),
                    'user_agent'    => Request::userAgent(),
                    'created_at'    => date("Y-m-d H:i:s"),
                );

                DB::table($this->table)->insert($data);
            }
        }
    }
}
