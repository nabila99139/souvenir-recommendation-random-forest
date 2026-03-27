<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Root admin already exists
        $existingAdmin = User::where('email', 'admin@test.com')->first();

        if ($existingAdmin) {
            $this->command->warn('⚠️  Root Admin account already exists!');
            $this->command->info('   Email: admin@test.com');
            $this->command->info('   Status: Account already created with ID: ' . $existingAdmin->id);

            // Determine role display name
            $roleDisplayName = $existingAdmin->getRoleDisplayName();

            Log::warning('Root Admin account already exists', [
                'email' => 'admin@test.com',
                'existing_id' => $existingAdmin->id,
                'existing_role' => $existingAdmin->role,
                'timestamp' => now(),
            ]);
        }

        // Only create new account if existing one is not Root
        if ($existingAdmin && $existingAdmin->isRoot()) {
            $this->command->info('ℹ️  Root Admin account already exists and has Root role. Skipping creation.');

            Log::info('Root Admin account skipped', [
                'email' => 'admin@test.com',
                'existing_id' => $existingAdmin->id,
                'reason' => 'Root role already assigned',
                'timestamp' => now(),
            ]);
        } else {
            // Create Root Admin account
            $rootAdmin = User::create([
                'name' => 'Root Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('password99'),
                'role' => User::ROLE_ROOT,
                'is_admin' => true,
                'authorized_by' => null, // System created account
            ]);

            Log::info('Initial Root Admin account created', [
                'user_id' => $rootAdmin->id,
                'email' => $rootAdmin->email,
                'name' => $rootAdmin->name,
                'role' => User::ROLE_ROOT,
                'timestamp' => now(),
            ]);

            $this->command->info('✅ Root Admin account created successfully!');
            $this->command->info('   Email: admin@test.com');
            $this->command->info('   Password: password99');
            $this->command->info('   Role: Root Admin');
        }

        // Create test seller accounts
        $this->createTestSellers();

        // Create sample souvenirs for sellers
        $this->call(SellerSouvenirsSeeder::class);
    }

    /**
     * Create test seller accounts
     */
    private function createTestSellers(): void
    {
        $testSellers = [
            [
                'name' => 'John Seller',
                'email' => 'seller@test.com',
                'password' => 'password99',
                'business_name' => 'Rainforest Crafts',
            ],
            [
                'name' => 'Jane Artisan',
                'email' => 'artisan@test.com',
                'password' => 'password99',
                'business_name' => 'Natural Gifts',
            ],
        ];

        foreach ($testSellers as $sellerData) {
            $existingSeller = User::where('email', $sellerData['email'])->first();

            if (!$existingSeller) {
                $seller = User::create([
                    'name' => $sellerData['name'],
                    'email' => $sellerData['email'],
                    'password' => Hash::make($sellerData['password']),
                    'role' => User::ROLE_SELLER,
                    'is_admin' => false,
                    'authorized_by' => null,
                ]);

                Log::info('Test seller account created', [
                    'user_id' => $seller->id,
                    'email' => $seller->email,
                    'name' => $seller->name,
                    'business_name' => $sellerData['business_name'] ?? null,
                    'timestamp' => now(),
                ]);

                $this->command->info('✅ Test seller account created: ' . $sellerData['email']);
            } else {
                $this->command->info('ℹ️  Test seller account already exists: ' . $sellerData['email']);
            }
        }
    }
}