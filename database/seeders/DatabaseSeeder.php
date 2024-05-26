<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguageSeeder::class);
        
        User::factory(10)->create();

        User::create([
            'first_name' => 'Ruddy Gonzalo',
            'last_name' => 'Quispe Huanca',
            'email' => 'ruddygonzqh@gmail.com',
            'password' => bcrypt('password'),
            'language_id' => 2,
        ]);
    }
}
