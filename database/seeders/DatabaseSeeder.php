<?php

namespace Database\Seeders;

use App\Models\Language;
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
        $language = Language::where('code', 'es')->first();

        User::create([
            'first_name' => 'Ruddy Gonzalo',
            'last_name' => 'Quispe Huanca',
            'email' => 'ruddygonzqh@gmail.com',
            'password' => bcrypt('password'),
            'language_id' => $language->id,
        ]);
    }
}
