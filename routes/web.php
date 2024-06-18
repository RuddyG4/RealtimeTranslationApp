<?php

use App\Http\Controllers\Auth\AuthController;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    $translationClient = new TranslationServiceClient();
    $supportedLanguages = $translationClient->getSupportedLanguages(
        TranslationServiceClient::locationName(env('GOOGLE_PROJECT_ID'), 'global'),
        ['displayLanguageCode' => 'en']
    );
    $languages = [];
    foreach ($supportedLanguages->getLanguages() as $language) {
        dump($language->getLanguageCode());
        // dump($language->displayName);
    }
    return 0;
});

Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');
