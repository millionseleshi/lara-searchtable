<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'form' => $this->faker->randomElement(['SOLUTION','INJECTABLE','TABLET']),
            'strength' => $this->faker->word,
            'reference_drug' => $this->faker->name,
            'application_number' => $this->faker->uuid,
            'drug_name' => $this->faker->domainWord,
            'active_ingredient' => $this->faker->text
        ];
    }
}
