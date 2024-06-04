<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\StaticImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        StaticImage::create();
        Currency::create([
            'name' => 'Saudi Riyal',
            'code' => 'SAR',
            'symbol' => '{"en":"SAR","ar":"\u0631.\u0633"}',
            'rate' => '1.000000',
            'active' => true
        ]);
    }
}
