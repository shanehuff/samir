<?php

namespace App\Http\Controllers\Api;

use App\Telegram\InteractsWithTelegramBot;
use App\Trading\Profit;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Message;
use App\Services\Livia;

class HandleTelegramHookController
{
    use InteractsWithTelegramBot;

    public function __invoke(Request $request): void
    {
        info(json_encode($request->all()));

        $chatId = $request->input('message.chat.id');
        $message = $request->input('message.text');


        if(str_contains($message, 'JSON Structure:')) {
            $livia = new Livia();
            $livia->sendPrompt($message);
            $response = $this->replyWithMessage($chatId, 'Sent request to Livia.');
        } elseif(str_contains($message, 'Create Account')) {

            $response = $this->replyWithMessage($chatId, 'Please enter your name:');

            info(json_encode($response));

            return;

        } elseif (str_contains($message, '/upscale')) {

            $livia = new Livia();
            $livia->sendUpscale();
            $response = $this->replyWithMessage($chatId, 'Sent request to Livia.');

        } elseif (str_contains($message, 'Profit today?')) {

            $this->replyWithProfitToday($chatId);

        } elseif (str_contains($message, 'Profit this month?')) {

            $this->replyWithProfitThisMonth($chatId);

        } elseif (str_contains($message, 'Profit this year?')) {

            $this->replyWithProfitThisYear($chatId);

        } else {
            $this->replyWithMessage($chatId, 'Hi there! I\'m Samir!');

            // Define the keyboard layout
            $keyboard = [
                ['Tell me a joke', 'Profit today?'],
                ['Profit this month?', 'Profit this year?'],
            ];

// Create a ReplyKeyboardMarkup object
            $replyMarkup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]);

// Send a message with the keyboard to the user
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'I have 4 options for you:',
                'reply_markup' => $replyMarkup,
            ]);

// Check the response for errors
            if ($response->isOk()) {
                // Message sent successfully
                info('Message sent with buttons!');
            } else {
                // Handle the error
                info('Error sending message: ' . $response->getDescription());
            }
        }

    }

    private function replyWithMessage(mixed $chatId, string $string): Message
    {
        return Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $string
        ]);
    }

    private function replyWithJoke(mixed $chatId): void
    {
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
    }

    private function replyWithProfitToday(mixed $chatId): void
    {
        // Get profit today
        $profitToday = Profit::query()
            ->whereDate('created_at', now()->toDateString())
            ->sum('net_profit');

        // Reply with profit today
        $this->replyWithMessage($chatId, 'Profit today: ' . Number::abbreviate($profitToday * 24700) . ' VND');
    }

    private function replyWithProfitThisMonth(mixed $chatId): void
    {
        // Get profit this month
        $profitThisMonth = Profit::query()
            ->whereMonth('created_at', now()->month)
            ->sum('net_profit');

        // Reply with profit this month
        $this->replyWithMessage($chatId, 'Profit this month: ' . Number::abbreviate($profitThisMonth * 24700) . ' VND');
    }

    private function replyWithProfitThisYear(mixed $chatId): void
    {
        // Get profit this year
        $profitThisYear = Profit::query()
            ->whereYear('created_at', now()->year)
            ->sum('net_profit');

        // Reply with profit this year
        $this->replyWithMessage($chatId, 'Profit this year: ' . Number::abbreviate($profitThisYear * 24700) . ' VND');
    }

}