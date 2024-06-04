<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

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
        $locales = LaravelLocalization::getSupportedLanguagesKeys();
        $alternatives_locales = [];

        foreach ($locales as $locale) {
            if($locale == LaravelLocalization::getCurrentLocale())
                $alternatives_locales[] = '';
            else
                $alternatives_locales[] = $locale;
        }

        foreach ($alternatives_locales as $alternative_locale) {
            if ($alternative_locale == '')
                $filepath = public_path('sitemap.xml');
            else
                $filepath = public_path('sitemap-'. $alternative_locale .'.xml');
            SitemapGenerator::create(env('APP_URL', 'https://www.youmats.com').'/'.$alternative_locale)
                ->hasCrawled(function (Url $url) use ($alternative_locale, $alternatives_locales, $locales) {
                    $lastSegment = $url->segment(count($url->segments()));
                    if($lastSegment == 'i')
                        return;
                    if ($alternative_locale == '') {
                        if (in_array($url->segment(1), $alternatives_locales) || in_array($url->segment(1), $locales)) {
                            return;
                        }
                    } elseif ($url->segment(1) != $alternative_locale) {
                        return;
                    }

                    return $url;
                })->writeToFile($filepath);

            $this->info('Done - ' . $alternative_locale);
        }
    }
}
