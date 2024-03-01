<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        for($i=1; $i<=1; $i++){
            Comment::create([
                'comments_content' => $faker->sentence,
                'user_id' => 1,
                'post_id' => 1,
            ]);
        }
    }
}
