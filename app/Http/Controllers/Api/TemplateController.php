<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class TemplateController extends Controller
{
    /**
     * @param $category_id
     * @param $product_id
     * @return mixed
     */
    public function loadData($category_id, $product_id = null) {
        $data = [];
        if(isset($category_id)) {
            $category = Category::where('id', $category_id)->firstorfail('template');
            $data['template'] = $category->template;
        }
        if((!is_null($product_id) && $product_id != 'null')) {
            $product = Product::where('id', $product_id)->firstorfail(['name', 'temp_name']);
            $data['name'] = $product->name ? $product->getTranslations('name') : null;
            $data['temp_name'] = $product->temp_name ? $product->getTranslations('temp_name') : null;
        }
        return response()->json($data);
    }
}
