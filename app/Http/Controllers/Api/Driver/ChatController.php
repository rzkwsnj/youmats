<?php

namespace App\Http\Controllers\Api\Driver;

use App\Events\PrivateMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function viewMessage($user_id) {
        $driver_id = Auth::guard('driver-api')->id();

        $sendHistory = UserMessage::with('message')->where([
            'sender_id' => $driver_id, 'sender_type' => 'driver', 'receiver_id' => $user_id, 'receiver_type' => 'user'
        ])->get()->collect();

        $receiveHistory = UserMessage::with('message')->where([
            'sender_id' => $user_id, 'sender_type' => 'user', 'receiver_id' => $driver_id, 'receiver_type' => 'driver'
        ])->get()->collect();

        return MessageResource::collection($sendHistory->merge($receiveHistory)->sortBy('created_at'));
    }

    public function sendMessage(Request $request) {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required'
        ]);

        $sender_id = Auth::guard('api')->id();
        $receiver_id = $request->receiver_id;
        $sender_type = 'driver';
        $receiver_type = 'user';

        $message = Message::create([
            'message' => $request->message
        ]);

        if(isset($message)) {
            try {
                $message->users()->attach($sender_id, [
                    'receiver_id' => $receiver_id,
                    'sender_type' => $sender_type,
                    'receiver_type' => $receiver_type
                ]);
                $sender = User::where('id', $sender_id)->first();

                $data = [];
                $data['sender_id'] = $sender_type. '_' .$sender_id;
                $data['sender_name'] = $sender->name;
                $data['receiver_id'] = $receiver_type. '_' .$receiver_id;
                $data['content'] = $message->message;
                $data['created_at'] = $message->created_at;
                $data['message_id'] = $message->id;

                event(new PrivateMessageEvent($data));

                return response()->json([
                    'data' => $data,
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } catch (\Exception $e) {
                $message->delete();
            }
        }
    }
}
