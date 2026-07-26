<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardLoginController extends Controller
{
    public function dashboardLogin(Request $request) {
        if (Auth::check()) {
            return view('Auth.dashboardlogin');
        }

        return redirect()->route('login');
    }

    public function dashboardWebsite() {
        return view('Auth.dashboardwebsitelogin');
    }
}
