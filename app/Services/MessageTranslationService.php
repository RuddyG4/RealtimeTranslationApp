<?php

namespace App\Services;

use App\Enums\MessageType;
use App\Enums\UserState;
use App\Models\ChatMember;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class MessageTranslationService
{
    /**
     * @param Collection $messages Messages must have the "originalText" relationship loaded
     * @return void
     */
    public function translateMessages($messages)
    {
        $textMessagesToTranslate = $messages->filter(function ($message) {
            return $message->type === MessageType::TEXT->value && $message->translatedText === null;
        });
        $audioMessagesToTranslate = $messages->filter(function ($message) {
            return $message->type === MessageType::AUDIO->value && $message->translatedAudio === null;
        });
        if ($textMessagesToTranslate->isNotEmpty()) {
            $this->translateTextMessages($textMessagesToTranslate);
        }
        if ($audioMessagesToTranslate->isNotEmpty()) {
            $this->translateAudioMessages($audioMessagesToTranslate);
        }
    }

    public function isMessageTranslated($message): bool
    {
        if ($message->type === MessageType::TEXT->value && $message->translatedText) {
            return true;
        }
        if ($message->type === MessageType::AUDIO->value && $message->translatedAudio) {
            return true;
        }
        return false;
    }

    /**
     * Verify if a message is translated into the user's language, if not, translates it.
     * Returns true if any message was translated, false otherwise.
     * @param Collection $messages 
     * @return bool
     */
    public function verifyAndTranslate(Collection $messages)
    {
        $newMessagesToTranslate = false;
        $ids = [];
        $messages->load(['translatedText', 'translatedAudio']);
        // Verify if the message is translated into the user's language
        foreach ($messages as $message) {
            if (!$this->isMessageTranslated($message)) {
                $ids[] = $message->id;
                $newMessagesToTranslate = true;
            }
        }
        // Filter untranslated messages
        $messagesToTranslate = $messages->whereIn('id', $ids);
        if ($newMessagesToTranslate) {
            $messagesToTranslate->load(['originalText', 'originalAudio']);
            $this->translateMessages($messagesToTranslate);
        }
        return $newMessagesToTranslate;
    }

    public function translateSingleMessage($message, array $languages)
    {
        $translationService = new TranslationService();
        switch ($message->type) {
            case MessageType::TEXT->value:
                foreach ($languages as $language) {
                    $translatedTexts = $translationService->translate([$message->originalText->content], $language->code);
                    $message->translatedText()->create([
                        'content' => $translatedTexts[0]->getTranslatedText(),
                        'is_original' => false,
                        'language_id' => $language->id
                    ]);
                }
                $message->load('textMessages');
                break;

            case MessageType::AUDIO->value:
                foreach ($languages as $language) {
                    if (!$message->originalAudio->transcription) {
                        // Convertir voz a texto (transcribir el audio)
                        $originalLanguage = $message->originalAudio->language;
                        $speectToTextService = new SpeechToTextService();
                        $transcription = $speectToTextService->transcript($message->originalAudio->relative_path, $originalLanguage->code);

                        // Guardamos la transcipcion.
                        $message->originalAudio->transcription = $transcription;
                        $message->originalAudio->save();

                        // Volvemos a cargar el audio original con la transcipcion.
                        $message->load('originalAudio');
                    }
                    // traducir la transcripcion
                    $translatedText = $translationService->translate([$message->originalAudio->transcription], $language->code);

                    // Convertir texto a voz
                    $textToSpeechService = new TextToSpeechService();
                    $relative_path = $textToSpeechService->synthesizeSpeech($translatedText[0]->getTranslatedText(), $language->code);
                    $message->translatedAudio()->create([
                        'path' => Storage::url($relative_path),
                        'relative_path' => $relative_path,
                        'is_original' => false,
                        'language_id' => $language->id,
                        'extension' => 'webm',
                    ]);
                }
                $message->load('audioMessages');
                break;
                // TODO: Implement other message types
            default:
                break;
        }
        return $message;
    }

    public function translateMessageForOnlineUsers($message)
    {
        $usersIds = ChatMember::where('chat_id', $message->chat_id)->pluck('user_id');
        $usersFromChat = User::whereIn('id', $usersIds)
            ->where('state', '<>', UserState::OFFLINE->value)
            ->with('language')
            ->get();
        $languagesToTranslate = [];
        foreach ($usersFromChat as $user) {
            switch ($message->type) {
                case MessageType::TEXT->value:
                    $message->load('originalText');
                    if ($user->language_id !== $message->originalText->language_id) {
                        $languagesToTranslate[] = $user->language;
                    }
                    break;

                case MessageType::AUDIO->value:
                    $message->load('originalAudio');
                    if ($user->language_id !== $message->originalAudio->language_id) {
                        $languagesToTranslate[] = $user->language;
                    }
                    break;
                    // TODO: Implement other message types
                default:
                    break;
            }
        }
        return $this->translateSingleMessage($message, $languagesToTranslate);
    }

    public function translateTextMessages($textMessages)
    {
        $texts = $textMessages->pluck('originalText.content')->toArray();
        $translationService = new TranslationService();
        $userLanguage = Language::find(auth()->user()->language_id);
        $translatedTexts = $translationService->translate($texts, $userLanguage->code);
        $i = 0;
        foreach ($textMessages as $message) {
            $message->translatedText()->create([
                'content' => $translatedTexts[$i]->getTranslatedText(),
                'is_original' => false,
                'language_id' => auth()->user()->language_id
            ]);
            $i++;
        }
    }

    public function translateAudioMessages($audioMessages)
    {
        $translationService = new TranslationService();
        $userLanguage = Language::find(auth()->user()->language_id);
        foreach ($audioMessages as $message) {
            if (!$message->originalAudio->transcription) {
                // Convertir voz a texto (transcribir el audio)
                $originalLanguage = $message->originalAudio->language;
                $speectToTextService = new SpeechToTextService();
                $transcription = $speectToTextService->transcript($message->originalAudio->relative_path, $originalLanguage->code);

                // Guardamos la transcipcion.
                $message->originalAudio->transcription = $transcription;
                $message->originalAudio->save();

                // Volvemos a cargar el audio original con la transcipcion.
                $message->load('originalAudio');
            }
            // traducir la transcripcion
            $translatedText = $translationService->translate([$message->originalAudio->transcription], $userLanguage->code);

            // Convertir texto a voz
            $textToSpeechService = new TextToSpeechService();
            $relative_path = $textToSpeechService->synthesizeSpeech($translatedText[0]->getTranslatedText(), $userLanguage->code);
            $message->translatedAudio()->create([
                'path' => Storage::url($relative_path),
                'relative_path' => $relative_path,
                'is_original' => false,
                'language_id' => $userLanguage->id,
                'extension' => 'webm',
            ]);
        }
    }
}
