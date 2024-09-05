<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    public function run()
    {
        Message::create([
            'content' => 'Hello from user 1 to user 2',
            'sender_id' => 13,
            'receiver_id' => 14,
        ]);

        Message::create([
            'content' => 'Reply from user 2 to user 1',
            'sender_id' => 14,
            'receiver_id' => 13,
        ]);
    }
}
