<?php

namespace App\Helpers;

use App\Mail\MailBuilder;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    public static function sendEmail($user, $subject, array $data, $template)
    {
        $email = Email::create([
            'user_id' => $user->id,
            'subject' => $subject,
            'data' => $data,
        ]);

        Mail::to($user->email)->send(new MailBuilder($email, $template));

        $email->update(['sent' => true]);
    }
}
