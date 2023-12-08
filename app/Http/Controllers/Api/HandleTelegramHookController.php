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

// Convert the incoming message to lowercase for case-insensitive matching
        $lowercaseMessage = strtolower($message);

// If $message is Hello or Xin chao
        if (preg_match('/(Hello|Xin chao)/i', $message)) {
            $this->replyWithMessage($chatId, 'Hello, I\'m Samir. What is your name?');

// Else if $message is My name is <name> or related to $name
        } elseif (preg_match('/(My name is|I am|I\'m|I\'m called|They call me|People call me|Call me) (\w+)/i', $message, $matches)) {
            $this->replyWithMessage($chatId, 'Hello ' . $matches[2] . ', nice to meet you!');

// Else if $message is How are you or How are you doing
        } elseif (preg_match('/(How are you|How are you doing)/i', $lowercaseMessage)) {
            $this->replyWithMessage($chatId, 'I\'m doing well, thank you! How about you?');

// Else if $message is What is your name
        } elseif (preg_match('/(What is your name|Your name)/i', $lowercaseMessage)) {
            $this->replyWithMessage($chatId, 'I\'m Samir, nice to chat with you!');

// Else if $message is Where are you from
        } elseif (preg_match('/(Where are you from)/i', $lowercaseMessage)) {
            $this->replyWithMessage($chatId, 'I am a virtual assistant, so I don\'t have a physical location. How can I help you today?');

// Else if $message is What can you do
        } elseif (preg_match('/(What can you do|What do you do)/i', $lowercaseMessage)) {
            $this->replyWithMessage($chatId, 'I can assist you with various tasks, answer questions, provide information, and more. Feel free to ask me anything!');

// Else if $message is Tell me a joke
        } elseif (preg_match('/(Tell me a joke|Share a joke)/i', $lowercaseMessage)) {
            $jokes = [
                'Why did the scarecrow win an award? Because he was outstanding in his field!',
                'How do you organize a space party? You planet!',
                'Why did the bicycle fall over? Because it was two-tired!',
                'What do you call a fish wearing a crown? A kingfish!',
                'Why did the math book look sad? Because it had too many problems!',
                'What did one ocean say to the other ocean? Nothing, they just waved!',
                'Why don\'t scientists trust atoms? Because they make up everything!',
                'What do you call fake spaghetti? An impasta!',
                'Why did the coffee file a police report? It got mugged!',
                'Why don\'t skeletons fight each other? They don\'t have the guts!',
                'I used to play piano by ear, but now I use my hands and fingers.',
                'Parallel lines have so much in common. It\'s a shame they\'ll never meet.',
                'Why did the tomato turn red? Because it saw the salad dressing!',
                'How do you organize a space party? You planet!',
                'Why did the bicycle fall over? Because it was two-tired!',
                'What do you call a snowman with a six-pack? An abdominal snowman!',
                'Why did the golfer bring two pairs of pants? In case he got a hole in one!',
                'What did the janitor say when he jumped out of the closet? Supplies!',
                'Why did the chicken join a band? Because it had the drumsticks!',
                'How does a penguin build its house? Igloos it together!',
                'What did one plate say to another plate? Tonight, dinner\'s on me!',
                'Why did the scarecrow become a successful motivational speaker? Because he was outstanding in his field!',
                'What do you get when you cross a snowman and a vampire? Frostbite!',
                'Why did the math book look sad? Because it had too many problems!',
                'What\'s orange and sounds like a parrot? A carrot!',
                'Why did the banana go to the doctor? It wasn\'t peeling well!',
                'What did one hat say to the other? Stay here, I\'m going on ahead!',
                'How do you make a tissue dance? You put a little boogie in it!',
                'Why did the computer keep its drink on the windowsill? Because it wanted a byte!',
                'What did one ocean say to the other ocean? Nothing, they just waved!',
                'Why did the scarecrow win an award? Because he was outstanding in his field!',
                'How do you organize a space party? You planet!',
                'Why did the bicycle fall over? Because it was two-tired!',
                'What do you call a fish wearing a crown? A kingfish!',
                'Why did the math book look sad? Because it had too many problems!',
                'What did one plate say to another plate? Tonight, dinner\'s on me!',
                'Why did the chicken join a band? Because it had the drumsticks!',
                'How does a penguin build its house? Igloos it together!',
                'What did the janitor say when he jumped out of the closet? Supplies!',
                'Why did the scarecrow become a successful motivational speaker? Because he was outstanding in his field!',
                'What do you get when you cross a snowman and a vampire? Frostbite!',
                'Why did the math book look sad? Because it had too many problems!',
                'What\'s orange and sounds like a parrot? A carrot!',
                'Why did the banana go to the doctor? It wasn\'t peeling well!',
                'What did one hat say to the other? Stay here, I\'m going on ahead!',
                'How do you make a tissue dance? You put a little boogie in it!',
                'Why did the computer keep its drink on the windowsill? Because it wanted a byte!',
                'What did one ocean say to the other ocean? Nothing, they just waved!',
                'Why did the scarecrow win an award? Because he was outstanding in his field!',
                'How do you organize a space party? You planet!',
                'Why did the bicycle fall over? Because it was two-tired!',
                'What do you call a fish wearing a crown? A kingfish!',
                'Why did the math book look sad? Because it had too many problems!',
                'What did one plate say to another plate? Tonight, dinner\'s on me!',
                'Why did the chicken join a band? Because it had the drumsticks!',
                'How does a penguin build its house? Igloos it together!',
                'What did the janitor say when he jumped out of the closet? Supplies!',
                'Why did the scarecrow become a successful motivational speaker? Because he was outstanding in his field!',
                'What do you get when you cross a snowman and a vampire? Frostbite!',
                'Why did the math book look sad? Because it had too many problems!',
                'What\'s orange and sounds like a parrot? A carrot!',
                'Why did the banana go to the doctor? It wasn\'t peeling well!',
                'What did one hat say to the other? Stay here, I\'m going on ahead!',
                'How do you make a tissue dance? You put a little boogie in it!',
                'Why did the computer keep its drink on the windowsill? Because it wanted a byte!',
                'What did one ocean say to the other ocean? Nothing, they just waved!',
                // Add more jokes...
            ];

            $randomJoke = $jokes[array_rand($jokes)];
            $this->replyWithMessage($chatId, $randomJoke);

// Add more conversation scenarios...


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