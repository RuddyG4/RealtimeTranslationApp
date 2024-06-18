<?php

namespace Database\Seeders;

use Google\Cloud\Translate\V3\TranslationServiceClient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translationClient = new TranslationServiceClient();
        $supportedLanguages = $translationClient->getSupportedLanguages(
            TranslationServiceClient::locationName(env('GOOGLE_PROJECT_ID'), 'global'),
            ['displayLanguageCode' => 'en']
        );
        $languages = [];
        foreach ($supportedLanguages->getLanguages() as $language) {
            $languages[] = [
                'code' => $language->getLanguageCode(),
                'name' => $language->getDisplayName()
            ];
        }
        DB::table('languages')->insert($languages);
    }
}
