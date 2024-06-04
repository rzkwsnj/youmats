<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LogFactory extends Factory
{

    protected $model = Log::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ip' => $this->faker->ipv4,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'url' => $this->faker->url,
            'type' => $this->faker->randomElement(['visit', 'chat', 'call', 'email']),
            'page_type' => $this->faker->randomElement([null, Category::class, Product::class, Vendor::class]),
            'page_id' => $this->faker->randomDigit,
            'is_subscribed' => $this->faker->boolean,
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now', null)
        ];
    }
}
