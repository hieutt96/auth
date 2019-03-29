<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CreateUser;
use Carbon\Carbon;

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
        // dd(1);
         // $job = (new CreateUser())
         //            ->delay(Carbon::now()->everyMinute());

        // dispatch($job);
        // CreateUser::dispatchNow();
        CreateUser::dispatch()->delay(now()->addMinutes(3));
        return view('home');
    }

    public function job(Request $request) {


    }
}
