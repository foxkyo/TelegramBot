<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function sethook($url = null)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/setWebhook?', [
            'form_params' => [
                'url' => 'http://' . $url . '/webhook',
            ]
        ]);

        $response = $response->getBody()->getContents();
        echo '<pre>';
        print_r($response);
    }

    public function webhook()
    {
        //region 接收Telegram 回應
        $updates = Telegram::commandsHandler(true);
        $params = [
            'chat_id' => $updates->getMessage()->getChat()->getId(),
            'text' => $updates->getMessage()->getText(),
            'text' => 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/setWebhook?',
            'username' => $updates->getMessage()->getChat()->getLastName()
                . $updates->getMessage()->getChat()->getFirstName(),
        ];
        //endregion


        $params['reply_markup'] = Telegram::replyKeyboardMarkup([
            'keyboard' => [
                ['🌞天氣', '🔧設定'],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        $this->BotTalk($params);

    }

    public function BotTalk($params)
    {
        //region 傳送輸入中狀態給使用者
        Telegram::sendChatAction([
            'chat_id' => $params['chat_id'],
            'action' => 'typing',
        ]);
        Telegram::sendMessage($params);
        return http_response_code(200);
    }


}
