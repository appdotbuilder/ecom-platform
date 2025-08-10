<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $smartphones = Category::where('slug', 'smartphones')->first();
        $laptops = Category::where('slug', 'laptops')->first();
        $headphones = Category::where('slug', 'headphones')->first();
        $fashion = Category::where('slug', 'fashion')->first();
        $homeGarden = Category::where('slug', 'home-garden')->first();

        $products = [
            // Smartphones
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'short_description' => 'Latest iPhone with titanium design',
                'description' => 'The iPhone 15 Pro features a titanium design, A17 Pro chip, and advanced camera system.',
                'sku' => 'IPH15PRO001',
                'barcode' => '1234567890123',
                'base_price' => 18999000,
                'cost_price' => 15000000,
                'stock_quantity' => 50,
                'weight' => 187,
                'is_featured' => true,
                'category_id' => $smartphones->id,
                'images' => ['/images/products/iphone-15-pro-1.jpg', '/images/products/iphone-15-pro-2.jpg'],
                'attributes' => ['color' => ['Natural Titanium', 'Blue Titanium', 'White Titanium'], 'storage' => ['128GB', '256GB', '512GB']],
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'short_description' => 'Premium Android smartphone with S Pen',
                'description' => 'Galaxy S24 Ultra with built-in S Pen, 200MP camera, and AI features.',
                'sku' => 'SGS24ULT001',
                'barcode' => '1234567890124',
                'base_price' => 19999000,
                'cost_price' => 16000000,
                'stock_quantity' => 30,
                'weight' => 232,
                'is_featured' => true,
                'category_id' => $smartphones->id,
                'images' => ['/images/products/galaxy-s24-ultra-1.jpg'],
                'attributes' => ['color' => ['Titanium Gray', 'Titanium Black', 'Titanium Violet'], 'storage' => ['256GB', '512GB', '1TB']],
            ],
            // Laptops
            [
                'name' => 'MacBook Air M3',
                'slug' => 'macbook-air-m3',
                'short_description' => 'Ultra-thin laptop with M3 chip',
                'description' => 'MacBook Air with M3 chip delivers exceptional performance in an incredibly thin design.',
                'sku' => 'MBAM3001',
                'barcode' => '1234567890125',
                'base_price' => 16999000,
                'cost_price' => 14000000,
                'stock_quantity' => 25,
                'weight' => 1240,
                'is_featured' => true,
                'category_id' => $laptops->id,
                'images' => ['/images/products/macbook-air-m3-1.jpg'],
                'attributes' => ['color' => ['Midnight', 'Starlight', 'Silver'], 'ram' => ['8GB', '16GB'], 'storage' => ['256GB', '512GB']],
            ],
            [
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'short_description' => 'Premium Windows ultrabook',
                'description' => 'Dell XPS 13 with InfinityEdge display and premium build quality.',
                'sku' => 'DXPS13001',
                'barcode' => '1234567890126',
                'base_price' => 22999000,
                'cost_price' => 18000000,
                'stock_quantity' => 20,
                'weight' => 1200,
                'category_id' => $laptops->id,
                'images' => ['/images/products/dell-xps-13-1.jpg'],
                'attributes' => ['color' => ['Platinum Silver', 'Graphite'], 'processor' => ['Intel i5', 'Intel i7'], 'ram' => ['8GB', '16GB']],
            ],
            // Headphones
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'short_description' => 'Wireless noise-canceling headphones',
                'description' => 'Industry-leading noise cancellation with premium sound quality.',
                'sku' => 'SOWH1000XM5',
                'barcode' => '1234567890127',
                'base_price' => 4999000,
                'cost_price' => 3500000,
                'stock_quantity' => 100,
                'weight' => 250,
                'is_featured' => true,
                'category_id' => $headphones->id,
                'images' => ['/images/products/sony-wh-1000xm5-1.jpg'],
                'attributes' => ['color' => ['Black', 'Silver'], 'connectivity' => ['Bluetooth 5.2', 'Wired']],
            ],
            // Fashion
            [
                'name' => 'Premium Cotton T-Shirt',
                'slug' => 'premium-cotton-t-shirt',
                'short_description' => 'Comfortable cotton t-shirt',
                'description' => 'High-quality cotton t-shirt with premium fabric and perfect fit.',
                'sku' => 'PCTS001',
                'barcode' => '1234567890128',
                'base_price' => 299000,
                'cost_price' => 150000,
                'stock_quantity' => 200,
                'weight' => 200,
                'category_id' => $fashion->id,
                'images' => ['/images/products/premium-tshirt-1.jpg'],
                'attributes' => ['color' => ['White', 'Black', 'Navy', 'Gray'], 'size' => ['S', 'M', 'L', 'XL', 'XXL']],
            ],
            // Home & Garden
            [
                'name' => 'Smart Air Purifier',
                'slug' => 'smart-air-purifier',
                'short_description' => 'WiFi-enabled air purifier with HEPA filter',
                'description' => 'Smart air purifier with app control and real-time air quality monitoring.',
                'sku' => 'SAP001',
                'barcode' => '1234567890129',
                'base_price' => 2999000,
                'cost_price' => 2000000,
                'stock_quantity' => 40,
                'weight' => 5000,
                'category_id' => $homeGarden->id,
                'images' => ['/images/products/air-purifier-1.jpg'],
                'attributes' => ['coverage' => ['30m²', '50m²'], 'filter_type' => ['HEPA H13'], 'connectivity' => ['WiFi', 'Bluetooth']],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}