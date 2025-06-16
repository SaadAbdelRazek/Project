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
            ['name' => 'Sofas', 'description' => 'Modern sofas', 'image' => 'sofas.jpg', 'category_id' => 1, 'priority' => 1, 'status' => 1],
            ['name' => 'Chairs', 'description' => 'Comfortable chairs', 'image' => 'chairs.jpg', 'category_id' => 1, 'priority' => 2, 'status' => 1],
            ['name' => 'Ceiling Lamps', 'description' => 'Decorative ceiling lamps', 'image' => 'ceiling-lamps.jpg', 'category_id' => 3, 'priority' => 3, 'status' => 1],
            ['name' => 'Wall Posters', 'description' => 'Artistic posters', 'image' => 'posters.jpg', 'category_id' => 2, 'priority' => 4, 'status' => 1],
            ['name' => 'Throw Pillows', 'description' => 'Colorful pillows', 'image' => 'pillows.jpg', 'category_id' => 6, 'priority' => 5, 'status' => 1],
            ['name' => 'Floor Lamps', 'description' => 'Modern floor lamps', 'image' => 'floor-lamps.jpg', 'category_id' => 3, 'priority' => 6, 'status' => 1],
            ['name' => 'Round Rugs', 'description' => 'Soft round rugs', 'image' => 'round-rugs.jpg', 'category_id' => 4, 'priority' => 7, 'status' => 1],
            ['name' => 'Glassware', 'description' => 'Elegant glass items', 'image' => 'glassware.jpg', 'category_id' => 10, 'priority' => 8, 'status' => 1],
            ['name' => 'Organizers', 'description' => 'Storage organizers', 'image' => 'organizers.jpg', 'category_id' => 9, 'priority' => 9, 'status' => 1],
            ['name' => 'Garden Lights', 'description' => 'LED lights for gardens', 'image' => 'garden-lights.jpg', 'category_id' => 8, 'priority' => 10, 'status' => 1],
        ];

        foreach ($subCategories as $sub) {
            SubCategory::create($sub);
        }
        //----------------------------------------------------------------------
        $ads = [
            ['title' => 'Summer Sale', 'image' => 'summer-sale.jpg', 'percentage' => 20, 'status' => 1],
            ['title' => 'Buy 1 Get 1', 'image' => 'b1g1.jpg', 'percentage' => 50, 'status' => 1],
        ];

        foreach ($ads as $ad) {
            Announcement::create($ad);
        }
        //----------------------------------------------------------------------
            $posts = [
                ['title' => 'Top 10 Living Room Ideas', 'image' => 'living-room.jpg', 'description' => 'Explore modern living room trends.', 'priority' => 1, 'status' => 1],
                ['title' => 'Best Lighting for Bedrooms', 'image' => 'bedroom-lighting.jpg', 'description' => 'Set the mood with the right lights.', 'priority' => 2, 'status' => 1],
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
