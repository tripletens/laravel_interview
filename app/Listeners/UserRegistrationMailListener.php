<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationSuccess;

class UserRegistrationMailListener
{
    public function handle($event)
    {
        // send registration success mail 

        Mail::to($event->user->email)->send(new RegistrationSuccess());
    }
}
