<?php
// Script de nettoyage pour supprimer tous les restaurants existants et créer uniquement les trois restaurants souhaités

// Chemin vers l'application Laravel (à placer à la racine du projet)
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Store;
use Illuminate\Support\Facades\DB;

// Désactiver les contraintes de clé étrangère temporairement
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Supprimer tous les restaurants existants
Store::truncate();

// Réactiver les contraintes de clé étrangère
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Créer uniquement les trois restaurants souhaités
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

echo "Nettoyage terminé. Seuls les 3 restaurants spécifiés existent maintenant dans la base de données.\n"; 