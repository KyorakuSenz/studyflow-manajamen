<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Student Task Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased flex flex-col md:flex-row min-h-screen md:h-screen md:overflow-hidden text-sm relative">

    @php
        $activeList = $tasks->whereIn('status', ['todo', 'in_progress']);
        $doneList   = $tasks->where('status', 'done');
        $totalAllTasks = $tasks->count();
        $progressPercent = $totalAllTasks > 0 ? round(($doneList->count() / $totalAllTasks) * 100) : 0;
    @endphp

    <!-- OVERLAY UNTUK SIDEBAR MOBILE -->
    <div id="mobileSidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden md:hidden"></div>

    <!-- 1. LEFT SIDEBAR -->
    <aside id="mainSidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transform -translate-x-full transition-transform duration-200 ease-in-out md:relative md:translate-x-0 md:flex-shrink-0">
        <div class="p-5 space-y-6 overflow-y-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-base shadow-sm">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight">StudyFlow</span>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Main Navigation -->
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50/80 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <i class="fa-regular fa-circle-check w-4 text-center"></i>
                        <span>My tasks</span>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $activeList->count() }}</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-regular fa-calendar w-4 text-center"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('courses.index') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-solid fa-book-open w-4 text-center"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('focus.index') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-regular fa-clock w-4 text-center"></i>
                    <span>Focus</span>
                </a>
            </nav>

            <!-- Daftar Mata Kuliah di Sidebar (Gaya Google Tasks Lists) -->
            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between px-3 mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Course Lists</span>
                    <a href="{{ route('courses.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">+ Add</a>
                </div>
                <div class="space-y-1">
                    <button type="button" onclick="filterByCourse('all', this)" class="course-filter-btn w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 transition">
                        <span class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>Semua Tugas</span>
                        </span>
                        <span class="text-[11px] text-slate-500">{{ $activeList->count() }}</span>
                    </button>
                    @foreach($courses as $c)
                        @php $cCount = $activeList->where('course_id', $c->id)->count(); @endphp
                        <button type="button" onclick="filterByCourse('{{ $c->id }}', this)" class="course-filter-btn w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                            <span class="flex items-center space-x-2 truncate">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" @style(["background-color: " . ($c->color_hex ?? '#4F46E5')])></span>
                                <span class="truncate">{{ $c->course_name }}</span>
                            </span>
                            <span class="text-[11px] text-slate-400">{{ $cCount }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-100 space-y-4">
            <div class="p-2">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-semibold text-slate-600">Task progress</span>
                    <span class="font-bold text-indigo-600">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" @style(["width: {$progressPercent}%"])></div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5">{{ $doneList->count() }} of {{ $totalAllTasks }} tasks completed</p>
            </div>

            <!-- User Profile & Logout -->
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="leading-tight overflow-hidden">
                        <p class="font-semibold text-xs text-slate-900 truncate max-w-[110px]">{{ Auth::user()->name ?? 'Student' }}</p>
                        <p class="text-[11px] text-slate-400">Student Account</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-600 p-1.5 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- 2. CENTER CONTENT (Google Tasks Style List) -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#F8FAFC]">
        <!-- Top Navbar Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-3">
                <button onclick="toggleSidebar()" class="md:hidden w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div>
                    <h1 class="text-sm md:text-base font-bold text-slate-900 truncate">My Tasks</h1>
                    <p class="text-[10px] md:text-xs text-slate-400">{{ now()->format('l, F j') }} · {{ $activeList->count() }} tugas aktif</p>
                </div>
            </div>

            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="relative hidden sm:block w-48 lg:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                    <input type="text" id="taskSearchInput" onkeyup="searchTasks()" placeholder="Cari tugas..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-4 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-400">
                </div>
                <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3.5 py-2 rounded-xl flex items-center space-x-1.5 shadow-sm transition">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Add a task</span>
                </button>
                <button onclick="toggleChatbot()" class="xl:hidden w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center hover:bg-indigo-100 transition" title="Study Assistant">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-3xl mx-auto space-y-5 pb-16">

                <!-- Flash Alert -->
                @if(session('success'))
                    <div id="flashAlert" class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="document.getElementById('flashAlert').remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Banner Petunjuk AI -->
                <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-4 text-white flex items-center justify-between shadow-sm">
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold flex items-center space-x-1.5">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                            <span>Malas isi form manual? Suruh Study Assistant aja!</span>
                        </p>
                        <p class="text-[11px] text-indigo-100">Ketik di chat kanan: <em>"Catatin tugas Laporan Praktikum matkul Pemrograman Framework deadline besok"</em></p>
                    </div>
                    <button type="button" onclick="sendQuickPrompt('Catatin tugas Review Materi untuk mata kuliah pertama deadline besok prioritas tinggi')" class="hidden sm:inline-flex bg-white/15 hover:bg-white/25 text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl whitespace-nowrap transition">
                        Coba Demo AI →
                    </button>
                </div>

                <!-- CARD UTAMA ALA GOOGLE TASKS -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <!-- Quick Add Inline Row -->
                    <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="w-full px-5 py-3.5 border-b border-slate-100 flex items-center space-x-3 text-left text-indigo-600 hover:bg-indigo-50/40 transition group">
                        <span class="w-5 h-5 rounded-full border-2 border-indigo-400 group-hover:bg-indigo-600 group-hover:border-indigo-600 group-hover:text-white flex items-center justify-center text-[10px] transition">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <span class="text-xs font-semibold">Tambah tugas baru...</span>
                    </button>

                    <!-- Daftar Tugas Aktif -->
                    <div id="activeTasksContainer" class="divide-y divide-slate-100">
                        @forelse($activeList as $task)
                            @php
                                $cColor = $task->course->color_hex ?? '#4F46E5';
                                $dueStr = is_string($task->due_date) ? $task->due_date : $task->due_date?->format('Y-m-d');
                                $dueFormatted = is_string($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->format('D, M j') : $task->due_date?->format('D, M j');
                            @endphp
                            <div class="task-row group px-5 py-3.5 hover:bg-slate-50/80 transition flex items-start justify-between gap-3"
                                 data-course="{{ $task->course_id }}"
                                 data-title="{{ strtolower($task->title) }}">
                                
                                <div class="flex items-start space-x-3.5 min-w-0 flex-1">
                                    <!-- Checkbox Bulat Google Tasks (Klik = Done) -->
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST" class="mt-0.5">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" title="Tandai selesai" class="w-5 h-5 rounded-full border-2 border-slate-300 hover:border-indigo-600 hover:bg-indigo-50 flex items-center justify-center text-transparent hover:text-indigo-600 transition">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        </button>
                                    </form>

                                    <!-- Detail Tugas -->
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="font-semibold text-xs text-slate-900 leading-snug">{{ $task->title }}</span>
                                            @if($task->priority === 'high')
                                                <span class="text-[10px] font-bold px-2 py-0.2 rounded-full bg-rose-50 text-rose-600 border border-rose-200/70">High</span>
                                            @elseif($task->priority === 'low')
                                                <span class="text-[10px] font-medium px-2 py-0.2 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/70">Low</span>
                                            @endif
                                        </div>

                                        @if($task->description)
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $task->description }}</p>
                                        @endif

                                        <div class="flex items-center flex-wrap gap-2 pt-0.5">
                                            <span class="inline-flex items-center space-x-1 text-[10px] font-semibold px-2 py-0.5 rounded-md" @style(["background-color: {$cColor}15", "color: {$cColor}"])>
                                                <span>{{ $task->course->course_code ?? '' }} · {{ $task->course->course_name ?? 'General' }}</span>
                                            </span>
                                            <span class="inline-flex items-center space-x-1 text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                                <i class="fa-regular fa-calendar text-[9px] text-slate-400"></i>
                                                <span>{{ $dueFormatted }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Edit & Delete -->
                                <div class="flex items-center space-x-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                    <button type="button"
                                        onclick="openEditModal({{ $task->id }}, {{ json_encode($task->title) }}, {{ $task->course_id }}, '{{ $dueStr }}', '{{ $task->priority ?? 'medium' }}', {{ json_encode($task->description ?? '') }}, '{{ $task->status }}')"
                                        title="Edit tugas"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus tugas" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center space-y-2">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
                                    <i class="fa-solid fa-check-double"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-700">Semua tugas beres!</p>
                                <p class="text-[11px] text-slate-400">Klik "Add a task" atau minta Study Assistant mencatat tugas baru.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- BAGIAN COMPLETED (COLLAPSIBLE ALA GOOGLE TASKS) -->
                    @if($doneList->count() > 0)
                        <div class="border-t border-slate-200/80 bg-slate-50/50">
                            <button type="button" onclick="toggleCompletedSection()" class="w-full px-5 py-3 flex items-center justify-between text-xs font-semibold text-slate-600 hover:bg-slate-100/70 transition">
                                <span class="flex items-center space-x-2">
                                    <i id="completedChevron" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform"></i>
                                    <span>Completed ({{ $doneList->count() }})</span>
                                </span>
                            </button>

                            <div id="completedTasksList" class="divide-y divide-slate-100 border-t border-slate-100">
                                @foreach($doneList as $task)
                                    <div class="task-row group px-5 py-3 flex items-center justify-between gap-3 opacity-75 hover:opacity-100 transition"
                                         data-course="{{ $task->course_id }}"
                                         data-title="{{ strtolower($task->title) }}">
                                        <div class="flex items-center space-x-3.5 min-w-0 flex-1">
                                            <!-- Uncheck (Kembalikan ke Todo) -->
                                            <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="todo">
                                                <button type="submit" title="Batalkan selesai" class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition">
                                                    <i class="fa-solid fa-check text-[10px]"></i>
                                                </button>
                                            </form>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-medium text-slate-500 line-through truncate">{{ $task->title }}</p>
                                                <span class="text-[10px] text-slate-400">{{ $task->course->course_name ?? 'General' }}</span>
                                            </div>
                                        </div>

                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas selesai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- BACKDROP AI ASSISTANT DI MOBILE -->
    <div id="aiChatBackdrop" onclick="toggleChatbot()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden xl:hidden"></div>

    <!-- 3. RIGHT SIDEBAR (AI Study Assistant Panel) -->
    <aside id="aiAssistantSidebar" class="fixed inset-y-0 right-0 z-40 w-full sm:w-80 bg-white border-l border-slate-200 flex flex-col justify-between transform translate-x-full transition-transform duration-200 ease-in-out xl:relative xl:translate-x-0 xl:flex-shrink-0">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900">Study assistant</h3>
                    <p class="text-[10px] text-emerald-600 font-medium flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span>Online · Auto-Task Enabled</span>
                    </p>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="xl:hidden text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- AI Chat Messages Area -->
        <div id="aiChatContainer" class="p-4 flex-1 overflow-y-auto space-y-4">
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-2.5 text-[11px] text-indigo-700 space-y-1">
                <p class="font-semibold"><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Perintah Cepat AI:</p>
                <p class="text-[10px] leading-relaxed">• "Catatin tugas [Nama Tugas] matkul [Nama MK] deadline besok"<br>• "Tambah matkul 402 Basis Data dosen Pak Budi hari Rabu"</p>
            </div>

            <div class="flex items-start space-x-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div class="bg-slate-100 text-slate-800 text-xs p-3 rounded-2xl rounded-tl-sm leading-relaxed">
                    Halo {{ strtok(Auth::user()->name ?? 'Student', ' ') }}! Sebutkan saja tugas atau mata kuliah yang mau dicatat, nanti langsung saya masukkan ke daftar kamu.
                    <span class="block text-[10px] text-slate-400 mt-1">Just now</span>
                </div>
            </div>
        </div>

        <!-- Input Box & Quick Chips -->
        <div class="p-3 border-t border-slate-100 space-y-2">
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1">
                <button type="button" onclick="sendQuickPrompt('Tugas apa saja yang belum selesai dan mana prioritasnya?')" class="text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full whitespace-nowrap transition">
                    Prioritas tugas
                </button>
                <button type="button" onclick="sendQuickPrompt('Buatkan rencana belajar hari ini berdasarkan tugas saya')" class="text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full whitespace-nowrap transition">
                    Rencana belajar
                </button>
            </div>

            <form id="aiChatForm" class="relative flex items-center">
                @csrf
                <input type="text" id="aiMessageInput" placeholder="Suruh AI catat tugas..." required autocomplete="off"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-12 py-2 text-xs text-slate-700 focus:outline-none focus:border-indigo-400">
                <button type="submit" id="aiSendBtn" class="absolute right-1.5 p-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 w-7 h-7 flex items-center justify-center shadow-xs transition">
                    <i class="fa-solid fa-arrow-up text-xs"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- 4. MODAL ADD NEW TASK -->
    <div id="taskModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Add new task</h3>
                    <p class="text-xs text-slate-400">Tambahkan tugas baru ke daftar kamu.</p>
                </div>
                <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Task Title</label>
                    <input type="text" name="title" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500" placeholder="Contoh: Membuat ERD Database">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Course</label>
                    <select name="course_id" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->course_code }} · {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Deadline Date</label>
                        <input type="date" name="due_date" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Priority</label>
                        <select name="priority" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                            <option value="low">🟢 Low</option>
                            <option value="medium" selected>🟡 Medium</option>
                            <option value="high">🔴 High</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Notes / Description (Optional)</label>
                    <textarea name="description" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500" placeholder="Catatan tambahan..."></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">Save task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. MODAL EDIT TASK (CRUD UPDATE) -->
    <div id="editTaskModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Edit task</h3>
                    <p class="text-xs text-slate-400">Perbarui detail tugas kamu.</p>
                </div>
                <button onclick="document.getElementById('editTaskModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form id="editTaskForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Task Title</label>
                    <input type="text" name="title" id="editTitle" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Course</label>
                    <select name="course_id" id="editCourseId" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->course_code }} · {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Deadline Date</label>
                        <input type="date" name="due_date" id="editDueDate" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Priority</label>
                        <select name="priority" id="editPriority" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                            <option value="low">🟢 Low</option>
                            <option value="medium">🟡 Medium</option>
                            <option value="high">🔴 High</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Notes / Description</label>
                    <textarea name="description" id="editDescription" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('editTaskModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">Update task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function toggleChatbot() {
            const aiSidebar = document.getElementById('aiAssistantSidebar');
            const aiBackdrop = document.getElementById('aiChatBackdrop');
            aiSidebar.classList.toggle('translate-x-full');
            aiBackdrop.classList.toggle('hidden');
        }

        function toggleCompletedSection() {
            const list = document.getElementById('completedTasksList');
            const chevron = document.getElementById('completedChevron');
            list.classList.toggle('hidden');
            chevron.classList.toggle('-rotate-90');
        }

        function openEditModal(id, title, courseId, dueDate, priority, description) {
            document.getElementById('editTaskForm').action = `/tasks/${id}`;
            document.getElementById('editTitle').value = title;
            document.getElementById('editCourseId').value = courseId;
            document.getElementById('editDueDate').value = dueDate;
            document.getElementById('editPriority').value = priority;
            document.getElementById('editDescription').value = description;
            document.getElementById('editTaskModal').classList.remove('hidden');
        }

        function filterByCourse(courseId, btn) {
            document.querySelectorAll('.course-filter-btn').forEach(el => {
                el.classList.remove('bg-slate-100', 'text-slate-800', 'font-semibold');
                el.classList.add('text-slate-600', 'font-medium');
            });
            btn.classList.add('bg-slate-100', 'text-slate-800', 'font-semibold');
            btn.classList.remove('text-slate-600', 'font-medium');

            document.querySelectorAll('.task-row').forEach(row => {
                if (courseId === 'all' || row.dataset.course === courseId) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function searchTasks() {
            const keyword = document.getElementById('taskSearchInput').value.toLowerCase();
            document.querySelectorAll('.task-row').forEach(row => {
                row.classList.toggle('hidden', !row.dataset.title.includes(keyword));
            });
        }

        // AJAX Chat Handler + Auto Reload saat AI menambah tugas/matkul
        const chatForm = document.getElementById('aiChatForm');
        const messageInput = document.getElementById('aiMessageInput');
        const chatContainer = document.getElementById('aiChatContainer');
        const sendBtn = document.getElementById('aiSendBtn');

        function sendQuickPrompt(promptText) {
            messageInput.value = promptText;
            chatForm.dispatchEvent(new Event('submit'));
        }

        chatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            const userBubble = `
                <div class="flex justify-end">
                    <div class="bg-indigo-600 text-white text-xs p-3 rounded-2xl rounded-tr-sm leading-relaxed max-w-[85%] shadow-xs">
                        ${message}
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', userBubble);
            messageInput.value = '';
            chatContainer.scrollTop = chatContainer.scrollHeight;

            sendBtn.disabled = true;
            const loadingId = 'loading-' + Date.now();
            const loadingBubble = `
                <div id="${loadingId}" class="flex items-start space-x-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                    <div class="bg-slate-100 text-slate-500 text-xs p-3 rounded-2xl rounded-tl-sm italic">
                        Memproses permintaan...
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', loadingBubble);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            try {
                const response = await fetch("{{ route('ai.chat') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                document.getElementById(loadingId)?.remove();

                if (data.status === 'success') {
                    const botBubble = `
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-sparkles"></i>
                            </div>
                            <div class="bg-slate-100 text-slate-800 text-xs p-3 rounded-2xl rounded-tl-sm leading-relaxed max-w-[85%]">
                                ${data.reply}
                                <span class="block text-[10px] text-slate-400 mt-1">${data.time}</span>
                            </div>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('beforeend', botBubble);

                    // Jika AI berhasil mengeksekusi create_task / create_course, otomatis muat ulang daftar
                    if (data.reload) {
                        setTimeout(() => window.location.reload(), 1300);
                    }
                } else {
                    chatContainer.insertAdjacentHTML('beforeend', `
                        <div class="text-[11px] text-rose-500 p-2 bg-rose-50 rounded-xl">${data.reply}</div>
                    `);
                }
            } catch (err) {
                document.getElementById(loadingId)?.remove();
                chatContainer.insertAdjacentHTML('beforeend', `
                    <div class="text-[11px] text-rose-500 p-2 bg-rose-50 rounded-xl">Terjadi kendala koneksi ke server.</div>
                `);
            } finally {
                sendBtn.disabled = false;
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>