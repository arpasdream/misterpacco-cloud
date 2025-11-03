<?php

namespace App\Jobs;

use App\Models\Utente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleMail;

class SendEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $users = Utente::all(); // Retrieve your users, adjust as needed

        $chunkedUsers = $users->chunk(100); // Split users into chunks of 100

        foreach ($chunkedUsers as $chunk) {

            foreach ($chunk as $user) {

                Mail::to($user->email)->send(new SampleMail($user)); // Send email using your Mailable

            }

        }

    }

}
