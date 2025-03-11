<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les 3 restaurants avec les adresses spécifiées
        $stores = [
            [
                'name' => '2 rue blainville',
                'address' => '2 rue blainville',
                'phone' => '01 23 45 67 89',
                'description' => 'Restaurant rue blainville',
                'is_active' => true,
            ],
            [
                'name' => '187 rue saint-jaques',
                'address' => '187 rue saint-jaques',
                'phone' => '01 23 45 67 90',
                'description' => 'Restaurant 187 rue saint-jaques',
                'is_active' => true,
            ],
            [
                'name' => '21 rue saint-jaques',
                'address' => '21 rue saint-jaques',
                'phone' => '01 23 45 67 91',
                'description' => 'Restaurant 21 rue saint-jaques',
                'is_active' => true,
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
