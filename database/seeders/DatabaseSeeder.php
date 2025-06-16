<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Announcement;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Package;
use App\Models\Post;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            ['name' => 'The Modern Minimalist', 'description' => 'Modern sofas', 'image' => 'subcategories/1.jpg', 'category_id' => 2, 'priority' => 1, 'status' => 1],
            ['name' => 'The Artistic (Bohemian / Eclectic)', 'description' => 'Comfortable chairs', 'image' => 'subcategories/2.jpg', 'category_id' => 2, 'priority' => 2, 'status' => 1],
            ['name' => 'The Industrial', 'description' => 'Decorative ceiling lamps', 'image' => 'subcategories/3.jpg', 'category_id' => 2, 'priority' => 3, 'status' => 1],
            ['name' => 'The Scandinavian Modern', 'description' => 'Artistic posters', 'image' => 'subcategories/4.jpg', 'category_id' => 2, 'priority' => 4, 'status' => 1],
            ['name' => 'The Classic Elegance', 'description' => 'Colorful pillows', 'image' => 'subcategories/5.jpg', 'category_id' => 2, 'priority' => 5, 'status' => 1],
            ['name' => 'The Rustic / Farmhouse', 'description' => 'Modern floor lamps', 'image' => 'subcategories/6.jpg', 'category_id' => 2, 'priority' => 6, 'status' => 1],
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
                ['title' => 'Stylish Storage Solutions', 'image' => 'posts/3.jpg', 'description' => 'Maximize space with elegant storage designs.', 'priority' => 3, 'status' => 1],
                ['title' => 'Kitchen Decor Tips', 'image' => 'posts/4.jpg', 'description' => 'Transform your kitchen into a cozy and modern space.', 'priority' => 4, 'status' => 1],
                ['title' => 'Decorating Small Spaces', 'image' => 'posts/5.jpg', 'description' => 'Creative ideas to decorate compact rooms beautifully.', 'priority' => 5, 'status' => 1],
                ['title' => 'Balcony Makeover Guide', 'image' => 'posts/6.jpg', 'description' => 'Turn your balcony into a relaxing retreat.', 'priority' => 6, 'status' => 1],
            ];

            foreach ($posts as $post) {
                Post::create($post);
            }
        //----------------------------------------------------------------------

        $vendors = [
            [
                'name' => 'Creative Decor',
                'email' => 'creative@vendor.com',
                'phone' => '01012345678',
                'address' => '12 El Tahrir St, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=1',
            ],
            [
                'name' => 'Modern Touch',
                'email' => 'moderntouch@vendor.com',
                'phone' => '01022334455',
                'address' => '5 Nasr City, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=2',
            ],
            [
                'name' => 'Elegant Spaces',
                'email' => 'elegant@vendor.com',
                'phone' => '01098765432',
                'address' => '45 Maadi, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=3',
            ],
            [
                'name' => 'HomeArt Egypt',
                'email' => 'homeart@vendor.com',
                'phone' => '01033445566',
                'address' => '23 Zamalek, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=4',
            ],
            [
                'name' => 'DécorPro',
                'email' => 'decorpro@vendor.com',
                'phone' => '01055667788',
                'address' => '9 Heliopolis, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=5',
            ],
        ];

        $users = [
            [
                'name' => 'Saad Abdelrazzek',
                'email' => 's.a@example.com',
                'phone' => '01111222333',
                'address' => '25 Downtown, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=6',
            ],
            [
                'name' => 'Shimaa Gamal',
                'email' => 's.g@example.com',
                'phone' => '01122334455',
                'address' => '8 Mohandessin, Giza',
                'photo' => 'https://i.pravatar.cc/150?img=7',
            ],
            [
                'name' => 'Baraa Abdelmoezz',
                'email' => 'b.a@example.com',
                'phone' => '01133445566',
                'address' => '60 Abbasia, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=8',
            ],
            [
                'name' => 'Karim Hazem',
                'email' => 'k.h@example.com',
                'phone' => '01144556677',
                'address' => '15 Garden City, Cairo',
                'photo' => 'https://i.pravatar.cc/150?img=9',
            ],
            [
                'name' => 'Ziad Essam',
                'email' => 'z.e@example.com',
                'phone' => '01155667788',
                'address' => '33 6th of October, Giza',
                'photo' => 'https://i.pravatar.cc/150?img=10',
            ],
        ];

        foreach ($vendors as $vendor) {
            User::create([
                'name' => $vendor['name'],
                'email' => $vendor['email'],
                'password' => Hash::make('password'),
                'phone' => $vendor['phone'],
                'address' => $vendor['address'],
                'photo' => $vendor['photo'],
                'role' => 'vendor',
                'is_active' => 1,
            ]);
        }

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'phone' => $user['phone'],
                'address' => $user['address'],
                'photo' => $user['photo'],
                'role' => 'user',
                'is_active' => 1,
            ]);
        }

        //----------------------------------------------------------------------

        $packages = [
            [
                'name' => 'Basic Vendor Plan',
                'items' => 'إضافة حتى 20 منتج، صفحة تعريفية للبراند، دعم فني عبر البريد',
                'price' => 49.99,
            ],
            [
                'name' => 'Standard Vendor Plan',
                'items' => 'إضافة حتى 100 منتج، صفحة براند احترافية، دعم مباشر، تحليل أداء المبيعات',
                'price' => 99.99,
            ],
            [
                'name' => 'Premium Vendor Plan',
                'items' => 'إضافة عدد غير محدود من المنتجات، حملات ترويج على المنصة، مدير حساب مخصص، تقارير تفصيلية',
                'price' => 149.99,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        //----------------------------------------------------------------------

        $brands = [
            [
                'name' => 'Decora Home',
                'image' => 'brands/1.png',
                'description' => 'Elegant furniture and home accents.',
                'priority' => 1,
                'status' => 1,
                'user_id' => 1,
            ],
            [
                'name' => 'Luxe Living',
                'image' => 'brands/2.jpeg',
                'description' => 'Luxury decor for modern homes.',
                'priority' => 2,
                'status' => 1,
                'user_id' => 2,
            ],
            [
                'name' => 'Urban Nest',
                'image' => 'brands/3.jpeg',
                'description' => 'Trendy and minimal decor solutions.',
                'priority' => 3,
                'status' => 1,
                'user_id' => 3,
            ],
            [
                'name' => 'NatureWood',
                'image' => 'brands/4.png',
                'description' => 'Rustic and eco-friendly wood products.',
                'priority' => 4,
                'status' => 1,
                'user_id' => 4,
            ],
            [
                'name' => 'BrightSpaces',
                'image' => 'brands/5.png',
                'description' => 'Smart lighting and vibrant design.',
                'priority' => 5,
                'status' => 1,
                'user_id' => 5,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }



        //----------------------------------------------------------------------
        $categories_ = [
            1 => 'Furniture',
            2 => 'Wall Art',
            3 => 'Lighting',
            4 => 'Rugs',
            5 => 'Curtains',
            6 => 'Bedding',
            7 => 'Mirrors',
            8 => 'Outdoor Decor',
            9 => 'Storage',
            10 => 'Tableware',
        ];

        $categoryProducts = [
            'Furniture' => ['Modern Sofa', 'Wooden Dining Table', 'Office Chair', 'Bookshelf', 'TV Stand'],
            'Wall Art' => ['Abstract Painting', 'Framed Poster', 'Canvas Print', 'Wall Clock Art', 'Metal Wall Sculpture'],
            'Lighting' => ['Table Lamp', 'Ceiling Light', 'Wall Sconce', 'LED Strip Light', 'Chandelier'],
            'Rugs' => ['Persian Rug', 'Modern Area Rug', 'Shaggy Carpet', 'Vintage Runner', 'Outdoor Mat'],
            'Curtains' => ['Blackout Curtains', 'Sheer Panels', 'Velvet Drapes', 'Patterned Curtains', 'Linen Curtains'],
            'Bedding' => ['Cotton Bedsheet Set', 'Duvet Cover', 'Pillow Set', 'Quilted Blanket', 'Mattress Protector'],
            'Mirrors' => ['Round Mirror', 'Full-Length Mirror', 'Wall Mirror with Frame', 'LED Vanity Mirror', 'Decorative Mirror'],
            'Outdoor Decor' => ['Garden Statue', 'Balcony Plant Stand', 'Outdoor Lantern', 'Wind Chime', 'Terracotta Planter'],
            'Storage' => ['Wicker Basket', 'Plastic Organizer', 'Storage Ottoman', 'Closet Shelf', 'Underbed Box'],
            'Tableware' => ['Ceramic Plate Set', 'Glass Tumblers', 'Cutlery Set', 'Serving Bowl', 'Wooden Tray'],
        ];

        $brands = \App\Models\Brand::pluck('id')->toArray();

        for ($catId = 1; $catId <= 10; $catId++) {
            $catName = $categories_[$catId]; // ✅ بدون -1
            $productNames = $categoryProducts[$catName]; // ✅ باستخدام الاسم كـ key

            for ($i = 0; $i < 5; $i++) {
                $imageSuffix = $i == 0 ? '' : '_' . str_repeat('i', $i);
                $image = "products/{$catId}{$imageSuffix}.jpg";

                Product::create([
                    'name' => $productNames[$i],
                    'image' => $image,
                    'description' => "High-quality {$catName} item: {$productNames[$i]}.",
                    'price' => rand(200, 1500),
                    'width' => rand(30, 100),
                    'height' => rand(30, 100),
                    'length' => rand(30, 100),
                    'num_in_stock' => rand(5, 30),
                    'status' => 1,
                    'priority' => rand(1, 5),
                    'category_id' => $catId,
                    'sub_category_id' => rand(1, 6),
                    'brand_id' => $brands[array_rand($brands)],
                    'acceptance_status' => 'accepted',
                    'is_in_super_deals' => rand(0, 1),
                    'is_in_mega_deals' => rand(0, 1),
                    'sale' => rand(0, 1) ? rand(10, 50) : 0,
                ]);
            }
        }



    }
}
