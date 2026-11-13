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

    <!-- OVERLAY UNTUK SIDEBAR MOBILE -->
    <div id="mobileSidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden md:hidden"></div>

    <!-- 1. LEFT SIDEBAR (Hidden on mobile by default, slide-in on toggle) -->
    <aside id="mainSidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transform -translate-x-full transition-transform duration-200 ease-in-out md:relative md:translate-x-0 md:flex-shrink-0">
        <div class="p-5 space-y-6 overflow-y-auto">
            <!-- App Logo + Close Button (Mobile) -->
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
                        <i class="fa-regular fa-rectangle-list w-4 text-center"></i>
                        <span>My tasks</span>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $todoTasks->count() + $inProgressTasks->count() }}</span>
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

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-100 space-y-4">
            @php
                $totalAllTasks = $todoTasks->count() + $inProgressTasks->count() + $doneTasks->count();
                $progressPercent = $totalAllTasks > 0 ? round(($doneTasks->count() / $totalAllTasks) * 100) : 0;
            @endphp

            <div class="p-2 border-t border-slate-100">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-semibold text-slate-600">Task progress</span>
                    <span class="font-bold text-indigo-600">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" @style(["width: {$progressPercent}%"])></div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5">{{ $doneTasks->count() }} of {{ $totalAllTasks }} tasks completed</p>
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

    <!-- 2. CENTER CONTENT (Kanban Board) -->
    <main class="flex-1 flex flex-col min-w-0 bg-white">
        <!-- Top Navbar Header -->
        <header class="h-16 border-b border-slate-200 px-4 md:px-8 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-3">
                <!-- Hamburger Button (Mobile Only) -->
                <button onclick="toggleSidebar()" class="md:hidden w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div>
                    <h1 class="text-sm md:text-base font-bold text-slate-900 truncate">Good day, {{ strtok(Auth::user()->name ?? 'Student', ' ') }}</h1>
                    <p class="text-[10px] md:text-xs text-slate-400">{{ now()->format('l, F j') }} · {{ $todoTasks->count() + $inProgressTasks->count() }} active</p>
                </div>
            </div>

            <div class="flex items-center space-x-2 md:space-x-3">
                <div class="relative hidden sm:block w-48 lg:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                    <input type="text" placeholder="Search tasks..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-10 py-1.5 text-xs text-slate-700 focus:outline-none focus:border-indigo-400">
                </div>
                <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3 py-2 rounded-xl flex items-center space-x-1.5 shadow-sm transition">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span class="hidden sm:inline">New task</span>
                </button>
                <!-- Toggle AI Assistant on Mobile -->
                <button onclick="toggleChatbot()" class="xl:hidden w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center hover:bg-indigo-100 transition" title="Study Assistant">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
            <!-- Header Row & Metric Badges -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">My tasks</h2>
                    <p class="text-xs text-slate-400">On track across all registered courses</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center space-x-1.5 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-xl text-[11px] text-amber-800 font-medium">
                        <i class="fa-solid fa-fire text-amber-500"></i>
                        <span>{{ $focusStreak ?? 0 }}d streak</span>
                    </div>
                    <div class="flex items-center space-x-1.5 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-xl text-[11px] text-emerald-800 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <span>{{ $doneTasks->count() }} done</span>
                    </div>
                    <div class="flex items-center space-x-1.5 bg-indigo-50 border border-indigo-200/60 px-2.5 py-1 rounded-xl text-[11px] text-indigo-800 font-medium">
                        <i class="fa-regular fa-clock text-indigo-500"></i>
                        <span>{{ $focusHours ?? 0 }}h focus</span>
                    </div>
                </div>
            </div>

            <!-- Kanban Columns (Responsive: 1 col on mobile, 3 col on md+) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pb-16 md:pb-0">
                <!-- Column 1: To Do -->
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100 flex flex-col min-h-[300px] md:min-h-[500px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span class="font-bold text-xs text-slate-700">To Do</span>
                            <span class="text-xs text-slate-400 font-semibold">{{ $todoTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 flex-1">
                        @if($todoTasks->count() > 0)
                            @foreach($todoTasks as $task)
                            <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs hover:shadow-sm transition space-y-2.5">
                                <div class="flex items-center justify-between">
                                    @php
                                      $cColor = $task->course->color_hex ?? '#4F46E5';
                                    @endphp
<span class="text-[10px] font-bold px-2 py-0.5 rounded-md" @style(["background-color: {$cColor}15", "color: {$cColor}"])>
                                        {{ $task->course->course_code ?? 'Course' }}
                                    </span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full 
                                        @if($task->priority === 'high') bg-rose-50 text-rose-600 border border-rose-200/60
                                        @elseif($task->priority === 'low') bg-emerald-50 text-emerald-600 border border-emerald-200/60
                                        @else bg-amber-50 text-amber-600 border border-amber-200/60 @endif">
                                        {{ ucfirst($task->priority ?? 'medium') }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 leading-snug">{{ $task->title }}</h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $task->description }}</p>
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                    <span class="text-rose-500 font-medium flex items-center space-x-1">
                                        <i class="fa-regular fa-calendar-check text-[10px]"></i>
                                        <span>{{ is_string($task->due_date) ? $task->due_date : $task->due_date?->format('M j, Y') }}</span>
                                    </span>
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="text-indigo-600 font-semibold hover:text-indigo-800">Start →</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-xs text-slate-400">No tasks in To Do</div>
                        @endif
                    </div>

                    <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="mt-4 w-full py-2 border border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-500 hover:bg-white hover:border-slate-400 transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Add task</span>
                    </button>
                </div>

                <!-- Column 2: In Progress -->
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100 flex flex-col min-h-[300px] md:min-h-[500px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="font-bold text-xs text-slate-700">In Progress</span>
                            <span class="text-xs text-slate-400 font-semibold">{{ $inProgressTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 flex-1">
                        @if($inProgressTasks->count() > 0)
                            @foreach($inProgressTasks as $task)
                            <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs hover:shadow-sm transition space-y-2.5">
                                <div class="flex items-center justify-between">
                                    @php
                                        $cColor = $task->course->color_hex ?? '#3B82F6';
                                    @endphp
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md" @style(["background-color: {$cColor}15", "color: {$cColor}"])>
                                        {{ $task->course->course_code ?? 'Course' }}
                                    </span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full 
                                        @if($task->priority === 'high') bg-rose-50 text-rose-600 border border-rose-200/60
                                        @elseif($task->priority === 'low') bg-emerald-50 text-emerald-600 border border-emerald-200/60
                                        @else bg-amber-50 text-amber-600 border border-amber-200/60 @endif">
                                        {{ ucfirst($task->priority ?? 'medium') }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 leading-snug">{{ $task->title }}</h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $task->description }}</p>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: 50%"></div>
                                </div>
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                    <span class="text-amber-600 font-medium flex items-center space-x-1">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        <span>Due {{ is_string($task->due_date) ? $task->due_date : $task->due_date?->format('M j') }}</span>
                                    </span>
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="text-emerald-600 font-semibold hover:text-emerald-800">Done ✓</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-xs text-slate-400">No active tasks</div>
                        @endif
                    </div>

                    <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="mt-4 w-full py-2 border border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-500 hover:bg-white hover:border-slate-400 transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Add task</span>
                    </button>
                </div>

                <!-- Column 3: Done -->
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100 flex flex-col min-h-[300px] md:min-h-[500px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-xs text-slate-700">Done</span>
                            <span class="text-xs text-slate-400 font-semibold">{{ $doneTasks->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 flex-1">
                        @if($doneTasks->count() > 0)
                            @foreach($doneTasks as $task)
                            <div class="bg-white/70 rounded-xl p-4 border border-slate-200/60 shadow-xs space-y-2 opacity-85">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md" @style([
                                     'background-color: ' . ($task->course->color_hex ?? '#10B981') . '15',
                                    'color: ' . ($task->course->color_hex ?? '#10B981')
                                    ])>
                                        {{ $task->course->course_code ?? 'Course' }}
                                    </span>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                            {{ ucfirst($task->priority ?? 'medium') }}
                                        </span>
                                        <span class="text-emerald-600 text-xs"><i class="fa-solid fa-check"></i></span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-xs text-slate-700 line-through leading-snug">{{ $task->title }}</h4>
                                <p class="text-[11px] text-slate-400 line-clamp-1">{{ $task->description }}</p>
                                <div class="pt-2 border-t border-slate-100 text-[10px] text-emerald-600 font-medium">
                                    Completed ✓
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center text-xs text-slate-400">No completed tasks yet</div>
                        @endif
                    </div>

                    <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="mt-4 w-full py-2 border border-dashed border-slate-300 rounded-xl text-xs font-semibold text-slate-500 hover:bg-white hover:border-slate-400 transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Add task</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- BACKDROP AI ASSISTANT DI MOBILE -->
    <div id="aiChatBackdrop" onclick="toggleChatbot()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden xl:hidden"></div>

    <!-- 3. RIGHT SIDEBAR (AI Study Assistant Panel) -->
    <aside id="aiAssistantSidebar" class="fixed inset-y-0 right-0 z-40 w-full sm:w-80 bg-white border-l border-slate-200 flex flex-col justify-between transform translate-x-full transition-transform duration-200 ease-in-out xl:relative xl:translate-x-0 xl:flex-shrink-0">
        <!-- Assistant Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900">Study assistant</h3>
                    <p class="text-[10px] text-emerald-600 font-medium flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span>Online · knows your task board</span>
                    </p>
                </div>
            </div>
            <!-- Close Button for Mobile AI Chat -->
            <button onclick="toggleChatbot()" class="xl:hidden text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- AI Chat Messages Area -->
        <div id="aiChatContainer" class="p-4 flex-1 overflow-y-auto space-y-4">
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-2.5 text-[11px] text-indigo-700 flex items-start space-x-2">
                <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5"></i>
                <span>Using your deadlines, course load, and focus history.</span>
            </div>

            <!-- Bot Welcome Bubble -->
            <div class="flex items-start space-x-2">
                <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div class="bg-slate-100 text-slate-800 text-xs p-3 rounded-2xl rounded-tl-sm leading-relaxed">
                    Good day, {{ strtok(Auth::user()->name ?? 'Student', ' ') }}! Ada yang bisa saya bantu seputar tugas atau jadwal kuliahmu?
                    <span class="block text-[10px] text-slate-400 mt-1">Just now</span>
                </div>
            </div>
        </div>

        <!-- Input Box & Quick Chips -->
        <div class="p-3 border-t border-slate-100 space-y-2">
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-1">
                <button type="button" onclick="sendQuickPrompt('Bantu susun rencana fokus belajar hari ini')" class="text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full whitespace-nowrap transition">
                    Break down task
                </button>
                <button type="button" onclick="sendQuickPrompt('Tugas apa saja yang belum selesai?')" class="text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full whitespace-nowrap transition">
                    Cek Tugas
                </button>
                <a href="{{ route('focus.index') }}" class="text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 px-2.5 py-1 rounded-full whitespace-nowrap transition">
                    Start focus
                </a>
            </div>

            <!-- Form Interaktif Chat AI -->
            <form id="aiChatForm" class="relative flex items-center">
                @csrf
                <input type="text" id="aiMessageInput" placeholder="Ask about your work..." required autocomplete="off"
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
                    <p class="text-xs text-slate-400">Add a task to your schedule and stay on track.</p>
                </div>
                <button onclick="document.getElementById('taskModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Task Title</label>
                    <input type="text" name="title" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500" placeholder="e.g. Submit HCI research plan">
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500" placeholder="Research methods, interview notes, etc."></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">Save task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Handlers & AI AJAX Script -->
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Chatbot Toggle
        function toggleChatbot() {
            const aiSidebar = document.getElementById('aiAssistantSidebar');
            const aiBackdrop = document.getElementById('aiChatBackdrop');
            const isClosed = aiSidebar.classList.contains('translate-x-full');
            if (isClosed) {
                aiSidebar.classList.remove('translate-x-full');
                aiBackdrop.classList.remove('hidden');
            } else {
                aiSidebar.classList.add('translate-x-full');
                aiBackdrop.classList.add('hidden');
            }
        }

        // AJAX Chat Handler
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

            // 1. User Bubble
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

            // 2. Loading Bubble
            sendBtn.disabled = true;
            const loadingId = 'loading-' + Date.now();
            const loadingBubble = `
                <div id="${loadingId}" class="flex items-start space-x-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                    <div class="bg-slate-100 text-slate-500 text-xs p-3 rounded-2xl rounded-tl-sm italic">
                        Sedang membaca data tugas & berpikir...
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', loadingBubble);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // 3. Request ke POST /ai/chat
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