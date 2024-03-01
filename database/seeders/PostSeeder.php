<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        for($i=1; $i<=2; $i++){
            Post::create([
                'title' => $faker->sentence,
                'news_content' => $faker->text,
                'user_id' => 1,
            ]);
        }
    }
}
