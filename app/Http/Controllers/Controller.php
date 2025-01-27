<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $credential = DB::table('credentials')->where('user_id', $user->id)->first();

        return view('dashboard', compact('user', 'credential'));
    }
}
