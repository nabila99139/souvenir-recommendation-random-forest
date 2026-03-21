<?php

namespace Database\Seeders;

use App\Models\Souvenir;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SouvenirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $souvenirs = [
            // Batik (Traditional Textile)
            [
                'name' => 'Batik Tulis Solo',
                'category' => 'batik',
                'price_range' => 'high',
                'description' => 'Traditional hand-drawn batik from Solo, Indonesia. Each piece is uniquely crafted with intricate patterns.',
                'image_path' => 'images/souvenirs/batik-tulis.jpg',
            ],
            [
                'name' => 'Batik Cap Jogja',
                'category' => 'batik',
                'price_range' => 'medium',
                'description' => 'Stamped batik from Yogyakarta featuring classic geometric patterns.',
                'image_path' => 'images/souvenirs/batik-cap.jpg',
            ],
            [
                'name' => 'Batik Shirt Unisex',
                'category' => 'batik',
                'price_range' => 'medium',
                'description' => 'Modern batik shirt suitable for both formal and casual occasions.',
                'image_path' => 'images/souvenirs/batik-shirt.jpg',
            ],

            // Handicrafts
            [
                'name' => 'Wayang Kulit Puppet',
                'category' => 'handicrafts',
                'price_range' => 'high',
                'description' => 'Traditional Indonesian shadow puppet made from leather with intricate carvings.',
                'image_path' => 'images/souvenirs/wayang-kulit.jpg',
            ],
            [
                'name' => 'Wooden Mask (Topeng)',
                'category' => 'handicrafts',
                'price_range' => 'medium',
                'description' => 'Hand-carved wooden mask from Cirebon with traditional motifs.',
                'image_path' => 'images/souvenirs/topeng.jpg',
            ],
            [
                'name' => 'Rattan Basket',
                'category' => 'handicrafts',
                'price_range' => 'low',
                'description' => 'Handwoven rattan basket perfect for storage or decoration.',
                'image_path' => 'images/souvenirs/rattan-basket.jpg',
            ],
            [
                'name' => 'Clay Pottery',
                'category' => 'handicrafts',
                'price_range' => 'low',
                'description' => 'Traditional clay pottery from Kasongan with earthy tones.',
                'image_path' => 'images/souvenirs/clay-pottery.jpg',
            ],

            // Food & Snacks
            [
                'name' => 'Kacang Telur Garuda',
                'category' => 'food',
                'price_range' => 'low',
                'description' => 'Indonesian favorite - peanuts coated in crispy flour with savory seasoning.',
                'image_path' => 'images/souvenirs/kacang-telur.jpg',
            ],
            [
                'name' => 'Dodol Garut',
                'category' => 'food',
                'price_range' => 'low',
                'description' => 'Traditional sweet sticky candy from Garut made from glutinous rice and palm sugar.',
                'image_path' => 'images/souvenirs/dodol-garut.jpg',
            ],
            [
                'name' => 'Kerupuk Udang',
                'category' => 'food',
                'price_range' => 'low',
                'description' => 'Crispy shrimp crackers perfect as a snack or side dish.',
                'image_path' => 'images/souvenirs/kerupuk-udang.jpg',
            ],
            [
                'name' => 'Kue Lapis',
                'category' => 'food',
                'price_range' => 'low',
                'description' => 'Layered cake with coconut milk and pandan flavor.',
                'image_path' => 'images/souvenirs/kue-lapis.jpg',
            ],
            [
                'name' => 'Bakpia Pathok',
                'category' => 'food',
                'price_range' => 'low',
                'description' => 'Sweet mung bean pastry from Yogyakarta with crispy outer layer.',
                'image_path' => 'images/souvenirs/bakpia.jpg',
            ],

            // Textiles & Clothing
            [
                'name' => 'Tenun Ikat Fabric',
                'category' => 'textiles',
                'price_range' => 'high',
                'description' => 'Traditional woven fabric from Flores with distinctive geometric patterns.',
                'image_path' => 'images/souvenirs/tenun-ikat.jpg',
            ],
            [
                'name' => 'Songket Palembang',
                'category' => 'textiles',
                'price_range' => 'high',
                'description' => 'Luxurious hand-woven fabric with gold threads from Palembang.',
                'image_path' => 'images/souvenirs/songket.jpg',
            ],
            [
                'name' => 'Ulos Batak',
                'category' => 'textiles',
                'price_range' => 'medium',
                'description' => 'Traditional handwoven cloth from North Sumatra with symbolic meanings.',
                'image_path' => 'images/souvenirs/ulos.jpg',
            ],

            // Jewelry & Accessories
            [
                'name' => 'Silver Jewelry from Kotagede',
                'category' => 'jewelry',
                'price_range' => 'high',
                'description' => 'Handcrafted silver jewelry from Yogyakarta with traditional motifs.',
                'image_path' => 'images/souvenirs/silver-jewelry.jpg',
            ],
            [
                'name' => 'Pearl Bracelet',
                'category' => 'jewelry',
                'price_range' => 'high',
                'description' => 'Natural pearl bracelet from Indonesian waters.',
                'image_path' => 'images/souvenirs/pearl-bracelet.jpg',
            ],
            [
                'name' => 'Coconut Shell Accessories',
                'category' => 'jewelry',
                'price_range' => 'low',
                'description' => 'Eco-friendly accessories made from coconut shells.',
                'image_path' => 'images/souvenirs/coconut-accessories.jpg',
            ],

            // Home Decor
            [
                'name' => 'Woven Lamp',
                'category' => 'home_decor',
                'price_range' => 'medium',
                'description' => 'Handwoven rattan lamp with Indonesian patterns.',
                'image_path' => 'images/souvenirs/woven-lamp.jpg',
            ],
            [
                'name' => 'Batik Table Runner',
                'category' => 'home_decor',
                'price_range' => 'medium',
                'description' => 'Batik table runner with traditional Javanese motifs.',
                'image_path' => 'images/souvenirs/batik-table-runner.jpg',
            ],
            [
                'name' => 'Wooden Carving Wall Art',
                'category' => 'home_decor',
                'price_range' => 'medium',
                'description' => 'Intricate wooden carving wall art from Bali.',
                'image_path' => 'images/souvenirs/wooden-carving.jpg',
            ],

            // Coffee & Spices
            [
                'name' => 'Kopi Luwak',
                'category' => 'coffee_spices',
                'price_range' => 'high',
                'description' => 'World-famous Indonesian civet coffee with unique smooth taste.',
                'image_path' => 'images/souvenirs/kopi-luwak.jpg',
            ],
            [
                'name' => 'Toraja Coffee',
                'category' => 'coffee_spices',
                'price_range' => 'medium',
                'description' => 'Premium coffee from Toraja highlands with rich aroma.',
                'image_path' => 'images/souvenirs/toraja-coffee.jpg',
            ],
            [
                'name' => 'Spice Set (Rempah)',
                'category' => 'coffee_spices',
                'price_range' => 'low',
                'description' => 'Authentic Indonesian spice set including nutmeg, clove, and cinnamon.',
                'image_path' => 'images/souvenirs/spice-set.jpg',
            ],
        ];

        foreach ($souvenirs as $souvenir) {
            Souvenir::create($souvenir);
        }

        $this->command->info('Sample souvenir data seeded successfully!');
    }
}
