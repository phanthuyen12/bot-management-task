@extends('admin.layouts.app')

@section('title', 'Tạo team mới')
@section('header', 'Tạo team mới')
@section('subheader', 'Tạo một nhóm mới để phân bổ thành viên và người hỗ trợ hiệu quả hơn')

@section('actions')
    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center gap-3">
            <div class="w-8 h-8 rounded-[10px] bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 flex items-center justify-center">
                <i class="fas fa-plus text-xs"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Thêm team mới</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Điền đầy đủ thông tin bên dưới</p>
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

            <form method="POST" action="{{ route('admin.teams.store') }}" class="space-y-5">
                @csrf

                <!-- Team Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Tên team <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        value="{{ old('name') }}"
                        class="block w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400"
                        placeholder="Ví dụ: Team Alpha...">
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Slug</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 text-sm">/</span>
                        </div>
                        <input type="text" id="slug" name="slug"
                            value="{{ old('slug') }}"
                            class="block w-full pl-8 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400"
                            placeholder="team-alpha">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Để trống sẽ tự động tạo từ tên team.</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mô tả</label>
                    <textarea id="description" name="description" rows="4"
                        class="block w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400 resize-none"
                        placeholder="Mô tả ngắn về team này...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="team_leader_telegram_user_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Team leader mặc định</label>
                    <div class="relative">
                        <select id="team_leader_telegram_user_id" name="team_leader_telegram_user_id"
                            class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors cursor-pointer">
                            <option value="">-- Chọn sau khi đã phân thành viên vào team --</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ (string) old('team_leader_telegram_user_id') === (string) $leader->id ? 'selected' : '' }}>
                                    {{ $leader->displayName() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Có thể để trống lúc tạo team. Sau khi gán thành viên, bạn quay lại để chọn leader.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
                        <i class="fas fa-plus"></i> Tạo team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
