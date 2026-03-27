<?php

namespace Database\Seeders;

use App\Models\Souvenir;
use App\Models\User;
use Illuminate\Database\Seeder;

class SellerSouvenirsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get seller users
        $sellers = User::where('role', User::ROLE_SELLER)->get();

        if ($sellers->isEmpty()) {
            $this->command->warn('No sellers found. Please create seller accounts first.');
            return;
        }

        // Sample souvenirs data with prices and view counts
        $sampleSouvenirs = [
            [
                'name' => 'Traditional Batik Shirt',
                'category' => 'clothing',
                'price_range' => 'medium',
                'price' => 250000,
                'description' => 'Handmade batik shirt with traditional patterns from local artisans.',
                'image_path' => 'images/souvenirs/batik-shirt.jpg',
                'image' => 'images/souvenirs/batik-shirt.jpg',
                'views' => 45,
            ],
            [
                'name' => 'Handwoven Basket',
                'category' => 'crafts',
                'price_range' => 'low',
                'price' => 75000,
                'description' => 'Eco-friendly handwoven basket made from natural materials.',
                'image_path' => 'images/souvenirs/woven-basket.jpg',
                'image' => 'images/souvenirs/woven-basket.jpg',
                'views' => 32,
            ],
            [
                'name' => 'Local Honey Set',
                'category' => 'food',
                'price_range' => 'medium',
                'price' => 175000,
                'description' => 'Pure organic honey collected from local rainforest bees.',
                'image_path' => 'images/souvenirs/honey-set.jpg',
                'image' => 'images/souvenirs/honey-set.jpg',
                'views' => 58,
            ],
            [
                'name' => 'Wooden Carving',
                'category' => 'crafts',
                'price_range' => 'high',
                'price' => 650000,
                'description' => 'Intricate wooden carving featuring rainforest wildlife.',
                'image_path' => 'images/souvenirs/wooden-carving.jpg',
                'image' => 'images/souvenirs/wooden-carving.jpg',
                'views' => 73,
            ],
            [
                'name' => 'Spice Collection',
                'category' => 'food',
                'price_range' => 'low',
                'price' => 125000,
                'description' => 'Premium spice collection including vanilla, cinnamon, and cardamom.',
                'image_path' => 'images/souvenirs/spice-collection.jpg',
                'image' => 'images/souvenirs/spice-collection.jpg',
                'views' => 41,
            ],
        ];

        // Distribute souvenirs among sellers
        foreach ($sellers as $seller) {
            // Randomly select 2-4 souvenirs for each seller
            $sellerSouvenirs = collect($sampleSouvenirs)
                ->random(rand(2, 4))
                ->toArray();

            foreach ($sellerSouvenirs as $souvenir) {
                // Generate random view counts for variety
                $randomViews = rand(10, 100);
                $randomPrice = $souvenir['price'] + rand(-10000, 10000); // Small price variation

                Souvenir::create([
                    'name' => $souvenir['name'],
                    'category' => $souvenir['category'],
                    'price_range' => $souvenir['price_range'],
                    'price' => $randomPrice > 0 ? $randomPrice : $souvenir['price'],
                    'description' => $souvenir['description'],
                    'image_path' => $souvenir['image_path'],
                    'image' => $souvenir['image'],
                    'views' => $randomViews,
                    'seller_id' => $seller->id,
                ]);
            }
        }

        $totalSouvenirs = Souvenir::count();
        $this->command->info("Created {$totalSouvenirs} souvenirs for {$sellers->count()} seller(s).");
    }
}