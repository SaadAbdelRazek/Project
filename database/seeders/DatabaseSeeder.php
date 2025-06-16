<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Post;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $categories = [
            ['name' => 'Furniture', 'description' => 'Modern and vintage furniture.', 'image' => 'categories/1.jpg', 'priority' => 1, 'status' => 1],
            ['name' => 'Wall Art', 'description' => 'Art pieces for walls.', 'image' => 'categories/2.jpg', 'priority' => 2, 'status' => 1],
            ['name' => 'Lighting', 'description' => 'Lamps and light fixtures.', 'image' => 'categories/3.jpg', 'priority' => 3, 'status' => 1],
            ['name' => 'Rugs', 'description' => 'Area rugs and carpets.', 'image' => 'categories/4.jpg', 'priority' => 4, 'status' => 1],
            ['name' => 'Curtains', 'description' => 'Stylish window curtains.', 'image' => 'categories/5.jpg', 'priority' => 5, 'status' => 1],
            ['name' => 'Bedding', 'description' => 'Bedsheets and pillow covers.', 'image' => 'categories/6.jpg', 'priority' => 6, 'status' => 1],
            ['name' => 'Mirrors', 'description' => 'Decorative mirrors.', 'image' => 'categories/7.jpg', 'priority' => 7, 'status' => 1],
            ['name' => 'Outdoor Decor', 'description' => 'Garden and balcony decor.', 'image' => 'categories/8.jpg', 'priority' => 8, 'status' => 1],
            ['name' => 'Storage', 'description' => 'Storage boxes and organizers.', 'image' => 'categories/9.jpg', 'priority' => 9, 'status' => 1],
            ['name' => 'Tableware', 'description' => 'Plates, glasses, and utensils.', 'image' => 'categories/10.jpg', 'priority' => 10, 'status' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
        //----------------------------------------------------------------------
        $subCategories = [
            ['name' => 'The Modern Minimalist', 'description' => 'Modern sofas', 'image' => 'subcategories/1.jpg', 'category_id' => 1, 'priority' => 1, 'status' => 1],
            ['name' => 'The Artistic (Bohemian / Eclectic)', 'description' => 'Comfortable chairs', 'image' => 'subcategories/2.jpg', 'category_id' => 1, 'priority' => 2, 'status' => 1],
            ['name' => 'The Industrial', 'description' => 'Decorative ceiling lamps', 'image' => 'subcategories/3.jpg', 'category_id' => 3, 'priority' => 3, 'status' => 1],
            ['name' => 'The Scandinavian Modern', 'description' => 'Artistic posters', 'image' => 'subcategories/4.jpg', 'category_id' => 2, 'priority' => 4, 'status' => 1],
            ['name' => 'The Classic Elegance', 'description' => 'Colorful pillows', 'image' => 'subcategories/5.jpg', 'category_id' => 6, 'priority' => 5, 'status' => 1],
            ['name' => 'The Rustic / Farmhouse', 'description' => 'Modern floor lamps', 'image' => 'subcategories/6.jpg', 'category_id' => 3, 'priority' => 6, 'status' => 1],
        ];

        foreach ($subCategories as $sub) {
            SubCategory::create($sub);
        }
        //----------------------------------------------------------------------
        $ads = [
            ['title' => 'Summer Sale', 'image' => 'announcements/1.png', 'percentage' => 20, 'status' => 1],
            ['title' => 'Summer Big Sale', 'image' => 'announcements/2.webp', 'percentage' => 70, 'status' => 1],
        ];

        foreach ($ads as $ad) {
            Announcement::create($ad);
        }
        //----------------------------------------------------------------------
            $posts = [
                ['title' => 'Top 10 Living Room Ideas', 'image' => 'posts/1.jpg', 'description' => 'Explore modern living room trends.', 'priority' => 1, 'status' => 1],
                ['title' => 'Best Lighting for Bedrooms', 'image' => 'posts/2.jpg', 'description' => 'Set the mood with the right lights.', 'priority' => 2, 'status' => 1],
            ];

            foreach ($posts as $post) {
                Post::create($post);
            }
        //----------------------------------------------------------------------

        //----------------------------------------------------------------------

        //----------------------------------------------------------------------

        //----------------------------------------------------------------------

    }
}
