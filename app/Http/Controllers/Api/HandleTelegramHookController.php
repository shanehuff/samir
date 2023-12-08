<?php

namespace App\Http\Controllers\Api;

use App\Telegram\InteractsWithTelegramBot;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class HandleTelegramHookController
{
    use InteractsWithTelegramBot;

    public function __invoke(Request $request): void
    {
        info(json_encode($request->all()));
        $chatId = $request->input('message.chat.id');
        $message = $request->input('message.text');

        // If $message is Hello or Xin chao
        if (preg_match('/(Hello|Xin chao)/i', $message)) {
            $this->replyWithMessage($chatId, 'Hello, I\'m Samir. What is your name?');

            // Else if $message is My name is <name> or related to $name

        } elseif (preg_match('/(My name is|I am|I\'m|I\'m called|They call me|People call me|Call me) (\w+)/i', $message, $matches)) {
            $this->replyWithMessage($chatId, 'Hello ' . $matches[2] . ', nice to meet you!');

            // Else if $message is How are you or How are you doing

        } elseif (preg_match('/(How are you|How are you doing)/i', $message)) {
            $this->replyWithMessage($chatId, 'I\'m doing fine, thank you.');

            // Else if $message is What time is it or What is the time

        } elseif (preg_match('/(What time is it|What is the time)/i', $message)) {
            $this->replyWithMessage($chatId, 'It is ' . date('h:i A'));

            // Else if $message is What is your name or What are you called

        } elseif (preg_match('/(What is your name|What are you called)/i', $message)) {
            $this->replyWithMessage($chatId, 'My name is Samir. Nice to meet you!');

            // Else if $message is What is the weather like or What is the weather like today

        } elseif (preg_match('/(What is the weather like|What is the weather like today)/i', $message)) {
            $this->replyWithMessage($chatId, 'It is sunny today.');

            // Else if $message is What is your favorite color or What is your favorite colour

        } elseif (preg_match('/(What is your favorite color|What is your favorite colour)/i', $message)) {
            $this->replyWithMessage($chatId, 'My favorite color is blue.');

            // Else if $message is What is your favorite food

        } elseif (preg_match('/(What is your favorite food)/i', $message)) {
            $this->replyWithMessage($chatId, 'My favorite food is pizza.');

            // Else if $message is What is your favorite movie

        } elseif (preg_match('/(What is your favorite movie)/i', $message)) {
            $this->replyWithMessage($chatId, 'My favorite movie is The Matrix.');

            // Else if $message is What is your favorite song

        } elseif (preg_match('/(What is your favorite song)/i', $message)) {
            $this->replyWithMessage($chatId, 'My favorite song is Bohemian Rhapsody.');
        } else {
            $this->replyWithMessage($chatId, 'Không hiểu, đợi anh Sơn nâng cấp thêm nha.');
        }

    }

    private function replyWithMessage(mixed $chatId, string $string): void
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $string
        ]);
    }
}