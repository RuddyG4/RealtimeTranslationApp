<?php

use App\Enums\MessageType;
use App\Http\Controllers\Auth\AuthController;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Illuminate\Support\Facades\Route;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use Illuminate\Support\Facades\Storage;

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding as TextToSpeechEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    if (!Storage::exists('audios/PJRrukq9337Ib7HO9zICT57IVPIqM0EARuaI8KV7.webm')) {
        return "no existe";
    }
    // $content = Storage::get('audio/PJRrukq9337Ib7HO9zICT57IVPIqM0EARuaI8KV7.webm');
    $content = Storage::get('audios/6o5hHbgKSBg8mHZYsAf4s76cMzXTzaKAZwGPEt6W.webm');
    // $content = Storage::get('audio/pCxhk1HZhbEBFRqq10MG3WBQ5V3EfoSxDbRnh14m.webm');
    $encoding = AudioEncoding::WEBM_OPUS;
    $sampleRateHertz = 48000;
    $languageCode = 'en';

    // get contents of a file into a string
    // $content = file_get_contents($audioFile);

    // set string as audio content
    $audio = (new RecognitionAudio())
        ->setContent($content);

    // set config
    $config = (new RecognitionConfig())
        ->setEncoding($encoding)
        ->setSampleRateHertz($sampleRateHertz)
        ->setLanguageCode($languageCode)
        ->setUseEnhanced(true)
        ->setModel('command_and_search');

    // create the speech client
    $client = new SpeechClient();

    // make the API call
    $response = $client->recognize($config, $audio);
    $results = $response->getResults();
    dump($results);

    $output = "";
    // print results
    foreach ($results as $result) {
        $alternatives = $result->getAlternatives();
        $mostLikely = $alternatives[0];
        $transcript = $mostLikely->getTranscript();
        $output = $output . $transcript;
        $confidence = $mostLikely->getConfidence();
        printf('Transcript: %s' . PHP_EOL, $transcript);
        printf('Confidence: %s' . PHP_EOL, $confidence);
    }

    $client->close();

    $textToSpeechClient = new TextToSpeechClient();

    $input = new SynthesisInput();
    $input->setText($output);
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode('en-US');
    $audioConfig = new AudioConfig();
    $audioConfig->setAudioEncoding(AudioEncoding::WEBM_OPUS);

    $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
    Storage::put('audios/test/test.webm', $resp->getAudioContent());
    // file_put_contents('test.mp3', $resp->getAudioContent());
    return $output;
});

Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');
