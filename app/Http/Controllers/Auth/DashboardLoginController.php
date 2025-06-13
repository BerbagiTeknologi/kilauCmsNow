<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardLoginController extends Controller
{
    public function dashboardLogin(Request $request) {
        if (session('user_token')) {
            return view('Auth.dashboardlogin'); // Jika token tersedia, tampilkan dashboard
        } else {
            return redirect()->route('login'); // Redirect ke login jika tidak ada token
        }
    }

    public function dashboardWebsite() {
        return view('Auth.dashboardwebsitelogin');
    }
}
