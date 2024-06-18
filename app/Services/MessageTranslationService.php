<?php

namespace App\Services;

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
        $texts = [];
        foreach ($messages as $message) {
            $texts[] = $message->originalText->content;
        }
        $translationService = new TranslationService();
        $userLanguage = Language::find(auth()->user()->language_id);
        $translatedTexts = $translationService->translate($texts, $userLanguage->code);
        foreach ($messages as $index => $message) {
            $message->translatedText()->create([
                'content' => $translatedTexts[$index]->getTranslatedText(),
                'is_original' => false,
                'language_id' => auth()->user()->language_id
            ]);
        }
    }

    public function isMessageTranslated($message): bool
    {
        if ($message->translatedText) {
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
        $messages->load('translatedText');
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
            $messagesToTranslate->load('originalText');
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
}
