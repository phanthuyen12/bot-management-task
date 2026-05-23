@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa team')
@section('header', 'Chỉnh sửa team')
@section('subheader', 'Cập nhật tên, slug, leader và thành viên hỗ trợ của nhóm')

@section('actions')
    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center gap-3">
            <div class="w-8 h-8 rounded-[10px] bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center justify-center">
                <i class="fas fa-layer-group text-xs"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $team->name }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">ID: #{{ $team->id }}</p>
            </div>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-400 px-4 py-3 rounded-[10px] mb-5 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Team Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tên team <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        value="{{ old('name', $team->name) }}"
                        class="block w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400"
                        placeholder="Nhập tên team...">
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Slug</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm">/</span>
                        </div>
                        <input type="text" id="slug" name="slug"
                            value="{{ old('slug', $team->slug) }}"
                            class="block w-full pl-8 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400"
                            placeholder="ten-team-slug">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Slug dùng để định danh trong URL. Để trống sẽ tự động tạo từ tên team.</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mô tả</label>
                    <textarea id="description" name="description" rows="4"
                        class="block w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400 resize-none"
                        placeholder="Mô tả ngắn về team này...">{{ old('description', $team->description) }}</textarea>
                </div>

                <div>
                    <label for="team_leader_telegram_user_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Team leader</label>
                    <div class="relative">
                        <select id="team_leader_telegram_user_id" name="team_leader_telegram_user_id"
                            class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors cursor-pointer">
                            <option value="">-- Chưa chọn team leader --</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ (string) old('team_leader_telegram_user_id', $team->team_leader_telegram_user_id) === (string) $leader->id ? 'selected' : '' }}>
                                    {{ $leader->displayName() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Bot sẽ ưu tiên hiện người này trong bước “Bạn cần ai hỗ trợ?”.</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Thành viên hiện có</label>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ $team->users->count() }} thành viên
                        </span>
                    </div>

                    <div class="rounded-[10px] border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($team->users->sortBy(fn ($member) => mb_strtolower($member->displayName())) as $member)
                            <div class="px-4 py-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $member->displayName() }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $member->username ? '@' . $member->username : 'Không có username' }}
                                    </p>
                                </div>
                                @if($team->team_leader_telegram_user_id === $member->id)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        Team Lead
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400">
                                Team này chưa có thành viên. Hãy vào trang quản lý người dùng để phân team trước.
                            </div>
                        @endforelse
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Muốn bot hiển thị danh sách khi client cần hỗ trợ, trước hết hãy gán user vào team ở mục quản lý thành viên.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
                        <i class="fas fa-save"></i> Lưu cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
