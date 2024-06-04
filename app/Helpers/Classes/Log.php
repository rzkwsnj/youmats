<?php

namespace App\Helpers\Classes;

use App\Models\Category;
use App\Models\Log as LogModel;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\Request;
use Stevebauman\Location\Facades\Location;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Log
{

    public static function set($type = '', $route = '', $id = '', $url = '', $origin = null, $User_Agent = null)
    {
        $ip = Request::ip();
        $location = Location::get($ip);
        if($location) {

            $coordinates = json_encode([$location->latitude, $location->longitude], JSON_UNESCAPED_UNICODE);
            (empty($url)) ?? $url = url()->current();

            $CrawlerDetect = new CrawlerDetect;
            $Is_crawler = $CrawlerDetect->isCrawler($User_Agent);

            LogModel::create([
                'ip' => $ip,
                'country' => $location->countryName,
                'city' => $location->cityName,
                'coordinates' => $coordinates,
                'url' => $url,
                'origin' => $origin,
                'type' => $type,
                'page_type' => self::GetClassName($route),
                'page_id' => $id,
                'vendor_id' => self::GetVendorData($route, $id)[0],
                'category_id' => self::GetVendorData($route, $id)[2],
                'is_subscribed' => self::GetVendorData($route, $id)[1],
                'user_agent' => $User_Agent,
                'crawler' => $CrawlerDetect->getMatches(),
                'created_at' => now()
            ]);
        }
    }

    private static function GetVendorData($route_name, $id)
    {
        switch ($route_name) {
            case 'category':
                return [null, 0, $id];
                break;
            case 'product':
                $model = Product::whereId($id)->first(['id', 'vendor_id', 'category_id']);
                return [$model->vendor->id, 0, $model->category_id];
                break;
            case 'vendor':
                $model = Vendor::whereId($id)->first('id');
                return [$id, 0, null];
                break;
            default:
                return [null, 0, null];
        }
    }

    private static function GetClassName($route_name)
    {
        switch ($route_name) {
            case 'category':
                return Category::class;
                break;
            case 'product':
                return Product::class;
                break;
            case 'vendor':
                return Vendor::class;
                break;
            default:
                return null;

        }
    }
}
