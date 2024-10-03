<?php

namespace App\Http\Controllers;

use App\Console\Commands\SendMessageCommand;
use App\Jobs\SendMessageJob;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = auth()->guard('web')->id();
        $user = User::where('id', $userId)->select([
            'id','name','email'
        ])->first();
        return view('home',[
            'user' => $user,
        ]);
    }
    public function messages():JsonResponse{
        $messages = Message::with('user')->get()->append('time');
        return response()->json($messages);
    }
    public function message(Request $request): JsonResponse{
        $userId = auth()->guard('web')->id();
        $message = Message::create([
            'user_id'=> $userId,
            'text' => $request->get('text'),
        ]);
        SendMessageJob::dispatch($message);
        return response()->json([
            'success' => true,
            'message' => "message created and job dispatched"
        ]);
    }
}
