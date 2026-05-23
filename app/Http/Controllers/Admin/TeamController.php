<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        $query = Team::query()->with('leader')->withCount('users');
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $teams = $query->orderBy('name')->paginate(10);

        return view('admin.teams.index', compact('teams', 'search'));
    }

    public function create()
    {
        $leaders = TelegramUser::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.teams.create', compact('leaders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:teams,slug'],
            'description' => ['nullable', 'string'],
            'team_leader_telegram_user_id' => ['nullable', 'exists:telegram_users,id'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Team::create($data);

        return redirect()->route('admin.teams.index')->with('success', 'Thêm team mới thành công.');
    }

    public function edit(Team $team)
    {
        $team->load(['leader', 'users']);
        $leaders = TelegramUser::where('team_id', $team->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.teams.edit', compact('team', 'leaders'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:teams,slug,' . $team->id],
            'description' => ['nullable', 'string'],
            'team_leader_telegram_user_id' => [
                'nullable',
                'exists:telegram_users,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($team) {
                    if ($value === null) {
                        return;
                    }

                    $isMember = TelegramUser::whereKey($value)
                        ->where('team_id', $team->id)
                        ->exists();

                    if (!$isMember) {
                        $fail('Team leader phải là thành viên thuộc team này.');
                    }
                },
            ],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $team->update($data);

        return redirect()->route('admin.teams.index')->with('success', 'Cập nhật team thành công.');
    }
}
