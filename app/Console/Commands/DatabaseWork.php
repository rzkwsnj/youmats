<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

class DatabaseWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command does some work to database.';

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
        $this->CopyProductImages();
    }

    private function CopyProductImages()
    {
        $this->line('Starting product images copying');

        $letsCount = [];

        $products = $this->getProductsWithImages();

        foreach($products as $product) {
            $currentProduct = Product::find($product->pro_id);
            $imageUrl = 'http://youmats.com/' . $product->path;

            if($currentProduct) {
                $productId = $currentProduct->id;

                try {
                    $currentProduct->addMediaFromUrl($imageUrl)->toMediaCollection(PRODUCT_PATH);
                    $this->info("Added [$imageUrl] to a product with ID [$productId]");
                }
                catch(UnreachableUrl $e) {
                    $this->info("The image could not be resolved. Product: $productId");
                }
            }
        }

        $this->info('Operation has completed successfully');
    }

    private function CopyArabicProducts()
    {
        $this->line('Starting arabic database copying...');

        $products = $this->getProducts(1);

        foreach($products as $product)
        {
            $currentProduct = Product::find($product->pro_id);

            if($currentProduct)
            {
                $currentProduct->setTranslation('name', 'ar', $product->pro_title);
                $currentProduct->setTranslation('desc', 'ar', $product->pro_description);
                $currentProduct->setTranslation('short_desc', 'ar', $product->pro_title);
                $currentProduct->setTranslation('meta_title', 'ar', $product->pro_title);
                $currentProduct->setTranslation('meta_desc', 'ar', $product->pro_title);
                $currentProduct->setTranslation('meta_keywords', 'ar', $product->pro_title);
                $currentProduct->save();
            }

            $this->line("Copied arabic product name. ID: $product->pro_id");
        }

        $this->info('Operation has been completed. successfully');
    }

    private function CopyEnglishProducts()
    {
        $this->line('Starting english database copying...');

        $englishProducts = $this->getProducts(2);

        foreach($englishProducts as $product)
        {
            $this->line("Copied english product name. ID: $product->pro_id");
            $newEnglishProduct = Product::find($product->pro_id);

            if($newEnglishProduct)
            {
                $newEnglishProduct->setTranslation('name', 'en', $product->pro_title);
                $newEnglishProduct->setTranslation('desc', 'en', $product->pro_description);
                $newEnglishProduct->setTranslation('short_desc', 'en', $product->pro_title);
                $newEnglishProduct->setTranslation('meta_title', 'en', $product->pro_title);
                $newEnglishProduct->setTranslation('meta_desc', 'en', $product->pro_title);
                $newEnglishProduct->setTranslation('meta_keywords', 'en', $product->pro_title);
                $newEnglishProduct->save();
            }

        }

        $this->info('Operation has been completed. successfully');
    }

    private function getProducts($language)
    {
        return DB::select(DB::raw("
            SELECT*FROM products P
                JOIN products_translate OP ON OP.pro_id = P.id
            WHERE OP.lang_id = $language
            ORDER BY P.id DESC;
        "));
    }

    private function getProductsWithImages()
    {
        return DB::select(DB::raw("
            SELECT*FROM old_products P
                JOIN attachments A ON A.id = P.small_img_id
            ORDER BY P.pro_id ASC;
        "));
    }
}
