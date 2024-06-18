<?php

namespace App\Services;
// require 'vendor/autoload.php';

use Google\Cloud\Translate\V3\TranslationServiceClient;

class TranslationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param string[] $contents                Required. The content of the input in string format.
     *                                                      We recommend the total content be less than 30,000 codepoints. 
     *                                                      The max length of this field is 1024. Use BatchTranslateText for larger text.
     * @param string $targetLanguageCode        Required. The ISO-639 language code to use for translation of the input text,
     *                                                      set to one of the language codes listed in Language Support.
     * @return Array Array with translated text
     */
    public function translate($content, $targetLanguage)
    {
        $translationClient = new TranslationServiceClient();
        $response = $translationClient->translateText(
            $content,
            $targetLanguage,
            TranslationServiceClient::locationName(env('GOOGLE_PROJECT_ID'), 'global')
        );
        return $response->getTranslations();
    }

    public function test()
    {
        return $this->translate(['Hello world', 'Can you do it?', 'three'], 'es');
    }
}
