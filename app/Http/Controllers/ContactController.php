<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'subject' => 'Contact mail',
        ];

        Mail::to('tee2dkay@gmail.com')->send(new ContactFormMail($data));

        return response()->json(['message' => 'Email sent successfully'], 200);
    }
}
