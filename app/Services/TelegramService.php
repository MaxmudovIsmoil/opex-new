<?php

namespace App\Services;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use App\Models\OrderAction; 
use App\Models\Order; 
use Illuminate\Support\Facades\Log;
use Exception;

class TelegramService
{
    public $telegram;
    
    public $adminChatId;
    public function __construct()
    {
        $botToken = config('telegram.bots.mybot.token');
        $this->adminChatId = config('telegram.bots.mybot.adminChatId');
        $this->telegram = new Api($botToken); 
    }

    public function sendOrderMessage(int $id): void
    {
        try {
            $recentActions = OrderAction::select('o.client', 'oa.stage', 'o.id', 'oa.status', 'i.name as instance_name', 'oa.created_at')
                ->from('order_actions as oa')
                ->join('orders as o', 'o.id', '=', 'oa.orderId')
                ->join('instances as i', 'i.id', '=', 'oa.instanceId')
                ->where('oa.orderId', $id)
                 ->where('oa.userId', '!=', 'o.userId')
                ->orderBy('oa.id', 'DESC')
                ->first();
            
                \Log::info('OA: '. json_encode($recentActions));

                switch ($recentActions->status) {
                    case 1: // PROCESSING
                        $recentActions->status = 'Processing';
                        break;
                
                    case 2: // ACCEPTED
                        $recentActions->status = 'Accepted';
                        break;
                
                    case 3: // DECLINED
                        $recentActions->status = 'Declined';
                        break;
                
                    case 4: // COMPLETED
                        $recentActions->status = 'Completed';
                        break;
                
                    case 5: // RESEND
                        $recentActions->status = 'Resend';
                        break;
                
                    case 6: // STOPPED
                        $recentActions->status = 'Stopped';
                        break;
                
                    default:
                        $recentActions->status = 'Unknown Status';
                        break;
                }
                

            // Retrieve the user based on the userId from the order
            $userIdChatId = Order::find($id)?->user?->chatId;
            Log::info($userIdChatId);
            $message = 'test';
            $message = "    *Order Details:*\n" .
                        "👤 *Client:* {$recentActions->client}\n" .
                        "📊 *Stage:* {$recentActions->stage}\n" .
                        "✅ *Status:* {$recentActions->status}\n" .
                        "🏢 *Instance Name:* {$recentActions->instance_name}\n" .
                        "📅 *Created At:* {$recentActions->created_at}";
           
        
            // Send the message to the user via Telegram
            $this->telegram->sendMessage([
                'chat_id' => $userIdChatId, // Use the user's chat ID
                'text' => $message,
            ]);
            
        }
        catch(\Exception $e) {
            \Log::info('error: '. json_encode($e->getMessage()));
        }
    }

    public function sendError(Exception $e){
        $errorMessage = sprintf(
            "*Error*: ` `%s`\n*Line*: `#%d`\n\n⚠️ *Please check the issue!*",
            $e->getMessage(),
            $e->getLine()
        );
        $this->telegram->sendMessage([
            'chat_id' => $this->adminChatId,
            'text' => $errorMessage,
            'parse_mode' => 'Markdown' 

        ]);
    }
}
