<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')->latest()->paginate(25);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load('user');

        return view('admin.reports.show', compact('report'));
    }

    public function review(Request $request, Report $report)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'points_earned' => ['required', 'integer', 'min:0'],
        ]);

        $previousPoints = $report->points_earned;
        $previousStatus = $report->status;

        $report->update($data);

        if ($report->user) {
            if ($previousStatus === 'approved' && $report->status !== 'approved') {
                $report->user->decrement('points', $previousPoints);
            }

            if ($report->status === 'approved') {
                $delta = $report->points_earned - ($previousStatus === 'approved' ? $previousPoints : 0);
                if ($delta !== 0) {
                    $report->user->increment('points', $delta);
                }
            }
        }

        return redirect()->route('admin.reports.show', $report)->with('success', 'Đã cập nhật đánh giá báo cáo.');
    }
}
