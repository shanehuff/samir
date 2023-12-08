<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interacts With Telegram Bot';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        /* @var Update $updates */
        $updates = Telegram::getUpdates();

        foreach ($updates as $update) {
            $id = $update->getChat()->getId();
            $response = Telegram::sendMessage([
                'chat_id' => $id,
                'text' => 'Hello World'
            ]);

            dd($response);
        }
    }
}
