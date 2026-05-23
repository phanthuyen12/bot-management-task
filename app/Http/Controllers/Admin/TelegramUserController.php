<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TelegramUser;
use Illuminate\Http\Request;

class TelegramUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $teamId = $request->query('team_id');
        
        $query = TelegramUser::with('team');
        
        if ($search) {
            $query->where('username', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
        }
        
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        
        $users = $query->orderByDesc('points')->paginate(10);
        $teams = Team::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'teams', 'search', 'teamId'));
    }

    public function edit(TelegramUser $user)
    {
        $teams = Team::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'teams'));
    }

    public function update(Request $request, TelegramUser $user)
    {
        $data = $request->validate([
            'team_id' => ['nullable', 'exists:teams,id'],
            'points' => ['required', 'integer', 'min:0'],
            'streak_count' => ['nullable', 'integer', 'min:0'],
            'best_streak' => ['nullable', 'integer', 'min:0'],
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thành viên thành công.');
    }
}
