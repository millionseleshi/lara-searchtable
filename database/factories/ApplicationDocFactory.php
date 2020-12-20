<?php

namespace Database\Factories;

use App\Models\ApplicationDoc;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationDocFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApplicationDoc::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product_id = Product::all()->pluck('id');
        return [
            'doc_type' => $this->faker->name,
            'submission_type' => $this->faker->sentence,
            'title' => $this->faker->userName,
            'doc_url' => $this->faker->url,
            'product_id' => $this->faker->randomElement($product_id),
        ];
    }
}
