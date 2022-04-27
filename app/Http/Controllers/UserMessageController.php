<?php

namespace App\Http\Controllers;

use App\Models\UserMessage;
use Illuminate\Http\Request;

class UserMessageController extends Controller
{
    
    // this section if for 'رسائل الإبلاغ عن إساءة'
    public function store(Request $request)
    {
        $attr = $request->validate([
            'user_id' => 'required|numeric',
            'message' => 'required|string',
        ]);

        return  UserMessage::create($attr)->load('sender');
    }
}
