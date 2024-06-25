<?php

namespace App\Services;
// require 'vendor/autoload.php';

use Illuminate\Support\Str;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
// use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Illuminate\Support\Facades\Storage;

class TextToSpeechService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param string $path                Required. Path to the audio file to transcript
     * @param string $targetLanguageCode        Required. The ISO-639 language code to use for translation of the input text,
     *                                                      set to one of the language codes listed in Language Support.
     * @return Array Array with translated text
     */
    public function synthesizeSpeech($text, $targetLanguage)
    {
        $textToSpeechClient = new TextToSpeechClient();

        $input = new SynthesisInput();
        $input->setText($text);
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode($targetLanguage);
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::WEBM_OPUS);
        $fileName = 'audios/translations/' . Str::random(10) . '.webm';

        $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
        Storage::put($fileName, $resp->getAudioContent());
        return $fileName;
        // file_put_contents('test.mp3', $resp->getAudioContent());
    }
}
