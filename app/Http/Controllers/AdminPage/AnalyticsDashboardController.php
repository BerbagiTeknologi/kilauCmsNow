<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\Request;

class AnalyticsDashboardController extends Controller
{
    public function index(Request $request, GoogleAnalyticsService $analytics)
    {
        $startDate = (string) $request->query('start_date', '28daysAgo');
        $endDate = (string) $request->query('end_date', 'today');
        $dashboard = $analytics->dashboard($startDate, $endDate);

        return view('AdminPage.Analytics.dashboard', compact('dashboard', 'startDate', 'endDate'));
    }
}
