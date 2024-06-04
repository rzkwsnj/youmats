<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMediaFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates media folders.';

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
        Log::info('Getting media entries...');

        $mediaItems = DB::select('SELECT * FROM media;');

        foreach($mediaItems as $row)
        {
            if(!file_exists(storage_path("app/public/" . $row->id)))
                mkdir(storage_path("app/public/" . $row->id));

            copy(storage_path('app/public/61/product_4984_1.png'), storage_path('app/public/'. $row->id .'/product_4984_1.png'));
            Log::info("Generating folders for $row->id");
        }

        Log::info('Done...');

        return 1;
    }
}
