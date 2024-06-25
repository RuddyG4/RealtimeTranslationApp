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
        
        User::factory(20)->create();
        $spanishLang = Language::where('code', 'es')->first();
        $englishLang = Language::where('code', 'en')->first();

        User::create([
            'first_name' => 'Ruddy Gonzalo',
            'last_name' => 'Quispe Huanca',
            'email' => 'ruddygonzqh@gmail.com',
            'password' => bcrypt('password'),
            'language_id' => $spanishLang->id,
            'photo' => "https://randomuser.me/api/portraits/men/" . fake()->randomNumber(2) . ".jpg"
        ]);
        User::create([
            'first_name' => 'Yordi',
            'last_name' => 'Condori Escalera',
            'email' => 'yordice77@gmail.com',
            'password' => bcrypt('password'),
            'language_id' => $englishLang->id,
            'photo' => "https://randomuser.me/api/portraits/men/" . fake()->randomNumber(2) . ".jpg"
        ]);
    }
}
