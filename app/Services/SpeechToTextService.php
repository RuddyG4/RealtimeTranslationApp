<?php

namespace App\Services;
// require 'vendor/autoload.php';

use Exception;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use Illuminate\Support\Facades\Storage;

class SpeechToTextService
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
    public function transcript($path, $targetLanguage)
    {
        if (!Storage::exists($path)) {
            return throw new Exception("Error transcripting: File not found");
        }
        $content = Storage::get($path);
        $encoding = AudioEncoding::WEBM_OPUS;
        $sampleRateHertz = 48000;
    
        // set string as audio content
        $audio = (new RecognitionAudio())
            ->setContent($content);
    
        // set config
        $config = (new RecognitionConfig())
            ->setEncoding($encoding)
            ->setSampleRateHertz($sampleRateHertz)
            ->setLanguageCode($targetLanguage)
            ->setUseEnhanced(true)
            ->setModel('command_and_search');
    
        // create the speech client
        $client = new SpeechClient();
    
        // make the API call
        $response = $client->recognize($config, $audio);
        $results = $response->getResults();
    
        $output = "";
        // print results
        foreach ($results as $result) {
            $alternatives = $result->getAlternatives();
            $mostLikely = $alternatives[0];
            $transcript = $mostLikely->getTranscript();
            $output = $output . $transcript;
        }
    
        $client->close();
        return $output;
    }
}
