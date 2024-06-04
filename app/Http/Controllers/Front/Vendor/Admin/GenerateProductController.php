<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\RequestGenerateProductRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\GenerateProduct;
use App\Notifications\GenerateProductsRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GenerateProductController extends Controller
{
    public function generate() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['categories'] = Category::whereIsRoot()->get();

        return view('vendorAdmin.product.generate')->with($data);
    }

    public function requestGenerate(RequestGenerateProductRequest $request) {
        $data = $request->validated();
        $data['vendor'] = Auth::guard('vendor')->user();

        $template = [
            'ar' => $this->mapData($data['template_ar']),
            'en' => $this->mapData($data['template_en'])
        ];

        $generate = GenerateProduct::create([
            'vendor_id' => $data['vendor']->id,
            'category_id' => $data['category_id'],
            'template' => $template
        ]);

        foreach(Admin::all() as $admin)
            $admin->notify(new GenerateProductsRequest($data['vendor'], $generate));

        Session::flash('success', __('vendorAdmin.success_request_generate_products'));
        return redirect()->route('vendor.dashboard');
    }

    /**
     * @param $entity
     * @return array
     */
    private function handleEntity($entity): array
    {
        return [
            'value' => (is_array($entity)) ? $this->convertArrayToFormattedString($entity) : $entity,
            'order' => ''
        ];
    }

    /**
     * @param $array
     * @return string
     */
    private function convertArrayToFormattedString($array): string
    {
        return implode('-', $array);
    }

    /**
     * @param $array
     * @return array
     */
    private function mapData($array): array
    {
        $template = [];
        foreach ($array as $row) {
            $template[] = $this->handleEntity($row);
        }
        return $template;
    }

}
