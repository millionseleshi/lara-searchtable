<?php

namespace Database\Seeders;

use App\Models\ApplicationDoc;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Product::factory(10)->create();
        ApplicationDoc::factory(10)->create();
    }
}
