<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GenerateProduct;

class GenerateProductController extends Controller
{
    public function loadData($category_id, $model_id = null) {
        $data = [];
        if(isset($category_id)) {
            $category = Category::where('id', $category_id)->firstorfail('template');
            $data['template'] = $category->template;
        }
        if((!is_null($model_id) && $model_id != 'null')) {
            $model = GenerateProduct::where('id', $model_id)->firstorfail(['template']);
            $data['current'] = $model->template;
        }
        return response()->json($data);
    }
}
