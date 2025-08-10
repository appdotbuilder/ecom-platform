<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Consumer electronics and gadgets',
                'sort_order' => 1,
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing, shoes, and accessories',
                'sort_order' => 2,
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Home improvement and garden supplies',
                'sort_order' => 3,
            ],
            [
                'name' => 'Health & Beauty',
                'slug' => 'health-beauty',
                'description' => 'Health products and beauty items',
                'sort_order' => 4,
            ],
            [
                'name' => 'Sports & Outdoor',
                'slug' => 'sports-outdoor',
                'description' => 'Sports equipment and outdoor gear',
                'sort_order' => 5,
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'description' => 'Books, movies, music, and games',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Add some subcategories
        $electronicsId = Category::where('slug', 'electronics')->first()->id;
        $subcategories = [
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'parent_id' => $electronicsId,
                'sort_order' => 1,
            ],
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'parent_id' => $electronicsId,
                'sort_order' => 2,
            ],
            [
                'name' => 'Headphones',
                'slug' => 'headphones',
                'parent_id' => $electronicsId,
                'sort_order' => 3,
            ],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }
    }
}