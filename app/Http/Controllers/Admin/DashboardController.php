<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\Report;
use App\Models\Team;
use App\Models\TelegramUser;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => TelegramUser::count(),
            'teamCount' => Team::count(),
            'reportCount' => Report::count(),
            'kpiCount' => Kpi::count(),
            'topUsers' => TelegramUser::orderByDesc('points')->take(5)->get(),
            'recentReports' => Report::with('user')->latest()->take(6)->get(),
        ]);
    }
}
