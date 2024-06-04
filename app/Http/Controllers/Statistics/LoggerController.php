<?php

namespace App\Http\Controllers\Statistics;

use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

ini_set('memory_limit', '256M');

class LoggerController
{

    private $Types_Of_Data = array('visit', 'call', 'chat', 'email');
    private $arabe_counties = array('Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Bahrain', 'Kuwait', 'Qatar', 'Oman', 'Jordan', 'Lebanon', 'Palestine', 'Syria', 'Iraq', 'Yemen', 'Egypt');

    public function getLogs() {


        //Most Popular vendor
        $data['top_vendors_values'] = $vendors_order = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->pluck('vendor_id')->toArray(), 100);
        $data['top_vendors'] = $this->OrderByArray(Vendor::with("media", "current_subscribes")->whereIn('id', array_keys($vendors_order)), $vendors_order)->select('id', 'name', 'email', 'slug')->get();

        //Most Popular Products
        $data['top_products_values'] = $products_order = $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class))->pluck('page_id')->toArray(), 100);
        $data['top_products'] = $this->OrderByArray(Product::with("media", "category", "category.ancestors", "vendor.current_subscribes")->whereIn('id', array_keys($products_order)), $products_order)->get();

        //Most Popular Vendors
        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('category_id'))->pluck('category_id')->toArray(), 20);
        $data['categories'] = $this->OrderByArray(Category::with("media", "ancestors")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();


        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            // general statistics
            $data['general'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]))->count();
            $data['unique'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]))->distinct('ip')->count();

            //Most Popular categories
            $data['top_categories_values'][$this->Types_Of_Data[$i]] = $categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->whereNotNull('category_id'))->pluck('category_id')->toArray(), 20);
            $data['top_categories'][$this->Types_Of_Data[$i]] = Category::whereIn('id', array_keys($categories_order))->pluck('name' ,'id')->toArray();

            //Most Popular categories unique
            $data['top_categories_values_unique'][$this->Types_Of_Data[$i]] = $categories_order_unique = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->whereNotNull('category_id'))->select(DB::raw('distinct `ip`, `category_id`'))->pluck('category_id')->toArray(), 20);
            $data['top_categories_unique'][$this->Types_Of_Data[$i]] = Category::whereIn('id', array_keys($categories_order_unique))->pluck('name' ,'id')->toArray();

            //Most Popular Products sub Data
            $data['top_products_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->pluck('page_id')->toArray());
            $data['top_products_sub_values_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->select(DB::raw('distinct `ip`, `page_id`'))->pluck('page_id')->toArray());

            //Most Popular Vendors sub Data
            $data['top_vendors_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->pluck('vendor_id')->toArray());
            $data['top_vendors_sub_values_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray());


            // Unique Hourly / daily report
            $data['days_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->groupBy('ip')->pluck('created_at')->toArray(), null, false);
            // Hourly / daily report
            $data['days'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->pluck('created_at')->toArray(), null, false);

        }

        return view('statistics.dashboard')->with($data);
    }

    public function getVendorsLogs() {

        $data['top_vendors_values'] = $vendors_order = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->pluck('vendor_id')->toArray(), 30);
        $data['top_vendors_values_unique'] = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray(), 30);

        $data['top_vendors_list'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($vendors_order)), $vendors_order)->get();
        $data['top_vendors'] = $data['top_vendors_list']->pluck('name')->toArray();



        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray(Product::whereNotNull('vendor_id')->pluck('vendor_id')->toArray(), 20);
        $data['categories'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();

        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            //Most Popular Vendors sub Data
            $data['top_vendors_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->pluck('vendor_id')->toArray());
            $data['top_vendors_sub_values_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray());

        }

        return view('statistics.vendor.all')->with($data);
    }



    public function SingleVendorLogs(int $Vendor_id) {

        $data['vendor'] = Vendor::where('id', $Vendor_id)->firstorfail();

        // general Maps
        $data['coordinates'] = $this->AddFilters(Log::where('vendor_id', $Vendor_id))->whereNotNull('coordinates')->pluck('coordinates')->toArray();

        //Countries using Youmats
        $data['countries'] = array_values($this->AddFilters(Log::where('vendor_id', $Vendor_id))->whereNotNull('country')->pluck('country')->unique()->toArray());

        //Most Popular Products
        $data['top_products_values'] = $products_order = $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class))->where('vendor_id', $Vendor_id)->pluck('page_id')->toArray(), 100);
        $data['top_products'] = $this->OrderByArray(Product::with("media", "category")->whereIn('id', array_keys($products_order)), $products_order)->get();

        //products distribution per categories
        $data['products_values'] = $products =  $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class))->where('vendor_id', $Vendor_id)->pluck('category_id')->toArray(), 100);
        $data['products'] = $this->OrderByArray(Category::with("media")->whereIn('id', array_keys($products)), $products)->pluck('name')->toArray();


        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray($this->AddFilters(Log::where('vendor_id', $Vendor_id)->whereNotNull('category_id'))->pluck('category_id')->toArray());
        $data['categories'] = $this->OrderByArray(Category::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();

        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            // general statistics
            $data['general'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->where('vendor_id', $Vendor_id))->count();
            $data['unique'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->where('vendor_id', $Vendor_id))->distinct('ip')->count();

            //Most Popular categories
            $data['top_categories_values'][$this->Types_Of_Data[$i]] = $categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->where('vendor_id', $Vendor_id)->whereNotNull('category_id'))->pluck('category_id')->toArray(), 20);
            $data['top_categories'][$this->Types_Of_Data[$i]] = Category::whereIn('id', array_keys($categories_order))->pluck('name' ,'id')->toArray();

            //Most Popular categories unique
            $data['top_categories_values_unique'][$this->Types_Of_Data[$i]] = $categories_order_unique = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->where('vendor_id', $Vendor_id)->whereNotNull('category_id'))->select(DB::raw('distinct `ip`, `category_id`'))->pluck('category_id')->toArray(), 20);
            $data['top_categories_unique'][$this->Types_Of_Data[$i]] = Category::whereIn('id', array_keys($categories_order_unique))->pluck('name' ,'id')->toArray();

            //Most Popular Products sub Data
            $data['top_products_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->pluck('page_id')->toArray());
            $data['top_products_sub_values_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->select(DB::raw('distinct `ip`, `page_id`'))->pluck('page_id')->toArray());

            // Unique Hourly / daily report
            $data['days_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->groupBy('ip')->pluck('created_at')->toArray(), null, false);

            // Hourly / daily report
            $data['days'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->pluck('created_at')->toArray(), null, false);

        }

        return view('statistics.vendor.index')->with($data);
    }


    public function getCategoryLogs() {

        $data['top_vendors_values'] = $vendors_order = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->pluck('vendor_id')->toArray(), 30);
        $data['top_vendors_values_unique'] = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray(), 30);
        $data['top_vendors_list'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($vendors_order)), $vendors_order)->get();
        $data['top_vendors'] = $data['top_vendors_list']->pluck('name')->toArray();

        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray(Product::whereNotNull('category_id')->pluck('category_id')->toArray(), 30);
        $data['categories'] = $this->OrderByArray(Category::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();


        return view('statistics.category.all')->with($data);
    }


    function SingleCategoryLogs(int $Category_id){

        $data['category'] = Category::with([
            'ancestors' => fn($q) => $q->with([ 'ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                                       ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug')
            ])->where('id', $Category_id)->firstOrFail();

        // general Maps
        $data['coordinates'] = $this->AddFilters(Log::where('category_id', $Category_id))->whereNotNull('coordinates')->pluck('coordinates')->toArray();

        //Countries using this category
        $data['countries'] = array_values($this->AddFilters(Log::where('category_id', $Category_id))->whereNotNull('country')->pluck('country')->unique()->toArray());

        //Most Popular Products
        $data['top_products_values'] = $products_order = $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class))->where('category_id', $Category_id)->pluck('page_id')->toArray(), 100);
        $data['top_products'] = $this->OrderByArray(Product::with("media", "category")->whereIn('id', array_keys($products_order)), $products_order)->get();

        //products distribution per vendor
        $data['products_values'] = $products =  $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class))->where('category_id', $Category_id)->pluck('vendor_id')->toArray(), 100);
        $data['products'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($products)), $products)->pluck('name')->toArray();


        $children_categories_ids = $data['category']->descendants()->pluck('id')->push($data['category']->id);
        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray($this->AddFilters(Log::whereIn('category_id', $children_categories_ids)->whereNotNull('category_id'))->pluck('category_id')->toArray());
        $data['categories'] = $this->OrderByArray(Category::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();

        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            // general statistics
            $data['general'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->where('category_id', $Category_id))->count();
            $data['unique'][$this->Types_Of_Data[$i]] = $this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->where('category_id', $Category_id))->distinct('ip')->count();

            //Most Popular categories
            $data['top_categories_values'][$this->Types_Of_Data[$i]] = $categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->where('category_id', $Category_id)->whereNotNull('vendor_id'))->pluck('vendor_id')->toArray());
            $data['top_categories'][$this->Types_Of_Data[$i]] = Vendor::whereIn('id', array_keys($categories_order))->pluck('name' ,'id')->toArray();

            $data['top_categories_values_unique'][$this->Types_Of_Data[$i]] = $categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $this->Types_Of_Data[$i])->where('category_id', $Category_id)->whereNotNull('vendor_id'))->groupBy('ip')->pluck('vendor_id')->toArray());
            $data['top_categories_unique'][$this->Types_Of_Data[$i]] = Vendor::whereIn('id', array_keys($categories_order))->pluck('name' ,'id')->toArray();

            //Most Popular Products sub Data
            $data['top_products_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('id', $Category_id)->where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->pluck('page_id')->toArray());

            // Hourly / daily report
            $data['days'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('category_id', $Category_id)->where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->pluck('created_at')->toArray(), null, false);

            $data['days_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('category_id', $Category_id)->where('type',$this->Types_Of_Data[$i]), true)
                                                            ->orderBy('created_at' , 'DESC')
                                                            ->groupBy('ip')->pluck('created_at')->toArray(), null, false);

        }


        return view('statistics.category.index')->with($data);

    }

    public function getProductsLogs() {

        $data['top_vendors_values'] = $vendors_order = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->pluck('vendor_id')->toArray(), 30);
        $data['top_vendors_values_unique'] = $this->ArrangeArray($this->AddFilters(Log::whereNotNull('vendor_id'))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray(), 30);

        $data['top_vendors_list'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($vendors_order)), $vendors_order)->get();
        $data['top_vendors'] = $data['top_vendors_list']->pluck('name')->toArray();



        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray(Product::whereNotNull('vendor_id')->pluck('vendor_id')->toArray(), 20);
        $data['categories'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();

        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            //Most Popular Vendors sub Data
            $data['top_vendors_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->pluck('vendor_id')->toArray());
            $data['top_vendors_sub_values_unique'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('vendor_id', array_keys($vendors_order)))->select(DB::raw('distinct `ip`, `vendor_id`'))->pluck('vendor_id')->toArray());

        }

        return view('statistics.product.all')->with($data);
    }


    public function OriginsLogs() {

        $data['coordinates'] = array_values($this->AddFilters(Log::where('id', '>' ,'0'))->whereNotNull('coordinates')->pluck('coordinates')->toArray());
        $data['visitor_origin'] = $this->ArrangeArray($this->AddFilters(Log::where('id', '>' ,'0'))->whereNotNull('origin')->pluck('origin')->toArray(), 50);
        $data['user_agent'] = $this->ArrangeArray($this->AddFilters(Log::where('id', '>' ,'0'))->whereNotNull('user_agent')->pluck('user_agent')->toArray(), 50);

        return view('statistics.origins')->with($data);
    }

    public function CounterLogs(string $count_type) {

        //Page type
        $data['page_type'] = $count_type;
        // general Maps
        $data['coordinates'] = array_values($this->AddFilters(Log::where('type', $count_type))->whereNotNull('coordinates')->pluck('coordinates')->unique()->toArray());

        //Countries using Youmats
        $data['countries'] = array_values($this->AddFilters(Log::where('type', $count_type))->whereNotNull('country')->pluck('country')->unique()->toArray());

        //Most Popular vendor
        $data['top_vendors_values'] = $vendors_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $count_type)->whereNotNull('vendor_id'))->pluck('vendor_id')->toArray());
        $data['top_vendors'] = $this->OrderByArray(Vendor::with("media")->whereIn('id', array_keys($vendors_order)), $vendors_order)->get();

        //Most Popular Products
        $data['top_products_values'] = $products_order = $this->ArrangeArray($this->AddFilters(Log::where('page_type', Product::class)->where('type', $count_type))->pluck('page_id')->toArray(), 100);
        $data['top_products'] = $this->OrderByArray(Product::with("media", "category")->whereIn('id', array_keys($products_order)), $products_order)->get();

        $data['categories_values'] = $whole_categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $count_type)->whereNotNull('category_id'))->pluck('category_id')->toArray());
        $data['categories'] = $this->OrderByArray(Category::with("media")->whereIn('id', array_keys($whole_categories_order)), $whole_categories_order)->get();

        // general statistics
        $data['general'][$count_type] = $this->AddFilters(Log::where('type', $count_type))->count();
        $data['unique'][$count_type] = $this->AddFilters(Log::where('type', $count_type))->distinct('ip')->count();

        //Most Popular categories
        $data['top_categories_values'][$count_type] = $categories_order = $this->ArrangeArray($this->AddFilters(Log::where('type', $count_type)->whereNotNull('category_id'))->pluck('category_id')->toArray() , 20);
        $data['top_categories'][$count_type] = Category::whereIn('id', array_keys($categories_order))->pluck('name' ,'id')->toArray();

        // last week statistics
        for ($o=0; $o < 7; $o++){
            $data['days'][$count_type][Carbon::now()->subDays($o)->format('Y-m-d')] = Log::where('type', $count_type)
                                                                  ->where('created_at', '>=', Carbon::now()->subDays($o)->format('Y-m-d'))
                                                                 ->where('created_at', '<', Carbon::now()->subDays($o-1)->format('Y-m-d'))
                                                                 ->count();
        }

        for ($i=0; $i < count($this->Types_Of_Data); $i++) {

            //Most Popular Products sub Data
            $data['top_products_sub_values'][$this->Types_Of_Data[$i]] = $this->ArrangeArray($this->AddFilters(Log::where('type',$this->Types_Of_Data[$i])->whereIn('page_id', array_keys($products_order)))->pluck('page_id')->toArray());

        }

        return view('statistics.metrics.counter')->with($data);
    }














    private function AddFilters($query , $date_and_time = false)
    {

        if(isset($_GET['date_from']) && $_GET['date_from'] != ""){
            $query->where('created_at', '>=', (new Carbon($_GET['date_from']))->format('Y-m-d H:i:s'));
         }else{
            $query->where('created_at', '>=', Carbon::now()->format('Y-m-d'));
        }
        if(isset($_GET['date_to']) && $_GET['date_to'] != "" ) { $query->where('created_at', '<=', (new Carbon($_GET['date_to']))->format('Y-m-d H:i:s')); }

        if(request()->get('country') == "All" OR !request()->get('country')) { $query; }
        elseif(request()->get('country') == "Arab-world") { $query->whereIn('country', $this->arabe_counties); }
        else{ $query->where('country', '=', str_replace('-', ' ', request()->get('country'))); }


        if($date_and_time == true){
            if(isset($_GET['date_from']) && $_GET['date_from'] != "" && isset($_GET['date_to']) && $_GET['date_to'] != ""){
                $to = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s', strtotime($_GET['date_to'])));
                $from = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', date('d-m-Y H:i:s', strtotime($_GET['date_from'])));

                if($to->diffInDays($from) > 1 ){
                    $query->select(DB::raw("DATE_FORMAT(created_at, '%m-%d') AS created_at"));
                }else{
                    $query->select(DB::raw("DATE_FORMAT(created_at, '%H') AS created_at"));
                }
            }else{
                $query->select(DB::raw("DATE_FORMAT(created_at, '%H') AS created_at"));
            }

        }

        return $query;
    }

    private function ArrangeArray($Array, $Limit = Null, $sort = true)
    {
        is_null($Limit) ? count($Array) : $Limit;

        $Array = array_replace($Array,array_fill_keys(array_keys($Array, null),''));

        $Array = array_count_values($Array);
        if($sort === true){ arsort($Array); }
        $Array = array_slice($Array, 0, $Limit, true);
        return $Array;
    }

    private function OrderByArray($query ,$products_order)
    {
        if(isset($products_order) && count($products_order)){
            return $query->orderByRaw("FIELD(id, ".implode(',', array_keys($products_order)).")");
        }else{
            return $query;
        }
    }
    private function DisplayUnique($collection)
    {
        $new_collection = $collection;

        return $new_collection;
    }

}
