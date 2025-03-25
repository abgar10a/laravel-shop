<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Random brands for different categories
        $brands = [
            'Apple', 'Samsung', 'Google', 'OnePlus', 'Xiaomi', 'Sony', 'LG', 'Dell', 'Lenovo',
            'HP', 'Asus', 'Microsoft', 'Huawei', 'Razer', 'Acer'
        ];

        // Phone models
        $phoneModels = [
            'iPhone 14', 'Samsung Galaxy S21', 'Google Pixel 6', 'OnePlus 9', 'Xiaomi Mi 11',
            'Nokia 8.3', 'Realme 8', 'Vivo X70', 'Motorola Edge', 'Oppo Reno 6'
        ];

        // Laptop models
        $laptopModels = [
            'MacBook Pro', 'Dell XPS 13', 'Lenovo ThinkPad X1', 'HP Spectre x360', 'Asus ZenBook 14',
            'Razer Blade 15', 'Microsoft Surface Laptop 4', 'Acer Predator Helios 300', 'HP Elite Dragonfly', 'Alienware m15'
        ];

        // Headphone models
        $headphoneModels = [
            'Sony WH-1000XM4', 'Bose QuietComfort 35 II', 'Beats Studio3', 'Sennheiser Momentum 3', 'JBL Live 650BTNC',
            'Audio-Technica ATH-M50X', 'Bang & Olufsen Beoplay H9', 'AKG N700NC', 'Bose SoundLink', 'Skullcandy Crusher ANC'
        ];

        // Display models
        $displayModels = [
            'Samsung Odyssey G7', 'LG UltraGear 27GN950', 'ASUS ROG Swift PG259QN', 'Acer Predator X27', 'BenQ EX3501R',
            'Dell UltraSharp U2720Q', 'ViewSonic Elite XG270QG', 'Gigabyte AORUS FI27Q', 'EIZO ColorEdge CG319X', 'Philips 278E1A'
        ];

        // Iterate and insert data
        for ($i = 0; $i < 50; $i++) {
            // Randomly assign a product type
            $typeId = rand(1, 4); // 1-Laptops, 2-Phones, 3-Headphones, 4-Displays

            // Assign the correct model based on the type
            switch ($typeId) {
                case 1:
                    $model = $faker->randomElement($laptopModels);
                    break;
                case 2:
                    $model = $faker->randomElement($phoneModels);
                    break;
                case 3:
                    $model = $faker->randomElement($headphoneModels);
                    break;
                case 4:
                    $model = $faker->randomElement($displayModels);
                    break;
            }

            // Get random brand for the selected model
            $brand = $faker->randomElement($brands);

            // Get random user_id and color_id
            $userId = \App\Models\User::inRandomOrder()->first()->id;
            $colorId = \App\Models\Color::inRandomOrder()->first()->id;

            // Insert the record
            Article::create([
                'brand' => $brand,
                'name' => $model,
                'quantity' => $faker->numberBetween(1, 100),
                'type_id' => $typeId,
                'price' => $faker->randomFloat(2, 10, 1000),
                'user_id' => $userId,
                'color_id' => $colorId,
            ]);
        }
    }

}
