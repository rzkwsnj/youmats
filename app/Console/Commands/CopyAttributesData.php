<?php

namespace App\Console\Commands;

use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyAttributesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribute:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy Attribute Data';

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
        $products = Product::where('attributes', '!=', null)
            ->where('attributes', '!=', '[]')->pluck('attributes', 'id')->toArray();

        foreach ($products as $key => $attributes) {
            foreach (json_decode($attributes, true) as $attribute) {
                $attributeValue = AttributeValue::find($attribute);
                if($attributeValue) {
                    DB::table('attribute_values_products')->insert([
                        'attribute_id' => $attribute,
                        'product_id' => $key,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return 0;
    }
}
