<?php

namespace App\Services;

use App\Enums\MessageType;
use App\Models\ChatMember;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
        // $this->translateAudioMessages($audioMessagesToTranslate);
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
        // $message->load('originalText');
        $translationService = new TranslationService();
        foreach ($languages as $language) {
            $translatedTexts = $translationService->translate([$message->originalText->content], $language->code);
            $message->translatedText()->create([
                'content' => $translatedTexts[0]->getTranslatedText(),
                'is_original' => false,
                'language_id' => $language->id
            ]);
        }
    }

    public function translateMessageForOnlineUsers($message)
    {
        $message->load('originalText');
        $usersIds = ChatMember::where('chat_id', $message->chat_id)->pluck('user_id');
        $usersFromChat = User::whereIn('id', $usersIds)
            ->with('language')
            ->get();
        $languagesToTranslate = [];
        foreach ($usersFromChat as $user) {
            if ($user->language_id !== $message->originalText->language_id) {
                $languagesToTranslate[] = $user->language;
            }
        }
        $this->translateSingleMessage($message, $languagesToTranslate);
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
        $texts = $audioMessages->pluck('originalAudio.transcription');
        $translationService = new TranslationService();
        $userLanguage = Language::find(auth()->user()->language_id);
        $translatedTexts = $translationService->translate($texts, $userLanguage->code);
        // POR TERMINAR, TODO
        foreach ($audioMessages as $index => $message) {
            $message->translatedAudio()->create([
                'content' => $translatedTexts[$index]->getTranslatedText(),
                'is_original' => false,
                'language_id' => auth()->user()->language_id
            ]);
        }
    }
}
