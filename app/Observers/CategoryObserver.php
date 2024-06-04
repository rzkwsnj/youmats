<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
//    public function created(Category $category) {
//        if(is_null($category->slug))
//            $category->slug = $category->name;
//        $category->save();
//    }

    public function updating(Category $category) {
        if($category->isDirty('template')){
            $category->allProducts()->update(['temp_name' => null]);
        }
    }

}
