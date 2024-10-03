<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\form;
use function Laravel\Prompts\textarea;
// use function Laravel\Prompts\number;
use App\Events\GotMessageEvent;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class SendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $responses = form()
            ->text(
                label:'Ur id:',required:true,name:'user_id',default:1
            )
            ->textarea(
                label:'Ur text:',required:true,name:'text'
            )
            ->submit();
        $messageData = DB::transaction(function () use ($responses){
            $messageId = Message::create([
                'user_id' => $responses['user_id'],
                'text' => $responses['text'],
                'time' => now(),
            ]);
            return [
                'id' => $messageId->id,
                'user_id' => $messageId->user_id,
                'text' => $messageId->text,
                'time' => $messageId->time,
            ];
        });
        // $message = new GotMessageEvent($messageData);
        GotMessageEvent::dispatch($messageData);
    }
}
