<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Relations\AttributeArticleRel;
use App\Models\Relations\AttributeTypeRel;
use Illuminate\Database\Seeder;

class AttributesArticleRelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        foreach (Article::all() as $article) {
            $articleType = $article->type_id;

            $attrIds = AttributeTypeRel::where('type_id', $articleType)->pluck('id')->toArray();

            $processors = ['i5-11', 'i5-12', 'i5-13', 'i5-14', 'i7-11', 'i7-12', 'i7-13', 'i7-14', 'i3-11', 'i3-12', 'i3-13', 'i3-14'];
            $rams = ['8', '12', '16', '24', '32'];
            $storages = ['256', '512', '1024'];
            $screenSizes = ['1920x1080', '1366x768', '1280x1024', '1600x1024', '1920x1080fhd'];
            $gpus = ['GeForce RTX 5090', 'GeForce RTX 5080', 'GeForce RTX 4080', 'GeForce RTX 4070', 'GeForce RTX 3080'];
            $battcapacities = ['3000', '3500', '3700', '3200', '4000', '4500'];
            $os = $article->brand === 'Apple' ? 'ios' : 'android';
            $contype = ['3.5mm', 'type-c', 'lightning'];
            $res = ['1920 × 1080 (Full HD)', '2560 × 1440 (2K/QHD)', '3840 × 2160 (4K UHD)'];
            $refrate = ['165', '60', '75', '144', '240'];


            foreach ($attrIds as $attrId) {
                $sims = $faker->numberBetween(1, 3);
                $wireless = $faker->boolean();
                $noise = $faker->boolean();
                $battlife = $faker->randomFloat(1, 2, 5);
                $megapixels = (string)$faker->numberBetween(10, 60);


                switch ($attrId) {
                    case 1:
                        $value = $faker->randomElement($processors);
                        break;
                    case 2:
                        $value = $faker->randomElement($rams);
                        break;
                    case 3:
                        $value = $faker->randomElement($storages);
                        break;
                    case 4:
                        $value = $faker->randomElement($screenSizes);
                        break;
                    case 5:
                        $value = $faker->randomElement($gpus);
                        break;
                    case 6:
                        $value = $megapixels;
                        break;
                    case 7:
                        $value = $faker->randomElement($battcapacities);
                        break;
                    case 8:
                        $value = $os;
                        break;
                    case 9:
                        $value = $sims;
                        break;
                    case 10:
                        $value = $wireless;
                        break;
                    case 11:
                        $value = $noise;
                        break;
                    case 12:
                        $value = $battlife;
                        break;
                    case 13:
                        $value = $faker->randomElement($contype);
                        break;
                    case 14 and 16:
                        $value = $faker->randomElement($res);
                        break;
                    case 15:
                        $value = $faker->randomElement($refrate);
                        break;
                }

                AttributeArticleRel::create([
                    'article_id' => $article->id,
                    'attribute_id' => $attrId,
                    'value' => $value,
                ]);
            }
        }
    }
}
