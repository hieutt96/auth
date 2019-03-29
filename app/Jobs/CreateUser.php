<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    

    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = new User;
        $user->name = rand(1, 100);
        $user->email = rand(1,100).'trunghieutran.bka@gmail.com';
        $user->password = bcrypt('hoaanhdao');
        $user->save();
    }
}
