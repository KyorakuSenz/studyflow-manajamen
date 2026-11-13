<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased flex h-screen overflow-hidden text-sm">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 z-20">
        <div class="p-5 space-y-6">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-base shadow-sm">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <span class="text-lg font-bold text-slate-900 tracking-tight">StudyFlow</span>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-regular fa-rectangle-list w-4 text-center"></i>
                    <span>My tasks</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-regular fa-calendar w-4 text-center"></i>
                    <span>Calendar</span>
                </a>
                <a href="{{ route('courses.index') }}" class="flex items-center space-x-3 px-3 py-2 font-semibold text-indigo-600 bg-indigo-50/80 rounded-xl">
                    <i class="fa-solid fa-book-open w-4 text-center"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('focus.index') }}" class="flex items-center space-x-3 px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition">
                    <i class="fa-regular fa-clock w-4 text-center"></i>
                    <span>Focus</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer Dinamis -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between px-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="leading-tight overflow-hidden">
                    <p class="font-semibold text-xs text-slate-900 truncate max-w-[110px]">{{ Auth::user()->name ?? 'Student' }}</p>
                    <p class="text-[11px] text-slate-400">Student Account</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-600 p-1.5 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 bg-white overflow-y-auto">
        <header class="h-16 border-b border-slate-200 px-8 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-900">Registered Courses</h1>
                <p class="text-xs text-slate-400">Total {{ $courses->sum('credits') }} Credits (SKS) registered</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="document.getElementById('courseModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3.5 py-2 rounded-xl flex items-center space-x-1.5 shadow-sm transition">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Add Course</span>
                </button>
                <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-3.5 py-2 rounded-xl flex items-center space-x-1.5 transition">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Back to Tasks</span>
                </a>
            </div>
        </header>

        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                @forelse($courses as $course)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-xs" style="background-color: {{ $course->color_hex }}">
                                {{ substr($course->course_code, 0, 2) }}
                            </div>
                            <div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md" style="background-color: {{ $course->color_hex }}15; color: {{ $course->color_hex }}">
                                    {{ $course->course_code }}
                                </span>
                                <h3 class="font-bold text-sm text-slate-900 mt-0.5">{{ $course->course_name }}</h3>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">{{ $course->credits }} SKS</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Lecturer</span>
                            <span class="font-semibold text-slate-700">{{ $course->lecturer_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Room</span>
                            <span class="font-semibold text-slate-700">{{ $course->room ?? 'Online / Lab' }}</span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-slate-200/60">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Class Schedule</span>
                            <span class="font-semibold text-indigo-600"><i class="fa-regular fa-calendar-days mr-1"></i> {{ $course->schedule_time }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                        <span>Active Tasks: <strong class="text-slate-800">{{ $course->tasks->where('status', '!=', 'done')->count() }} pending</strong></span>
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 font-semibold hover:underline">View tasks →</a>
                    </div>
                </div>
                @empty
                <div class="col-span-2 py-16 flex flex-col items-center justify-center text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 text-lg">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h3 class="font-bold text-sm text-slate-900">Belum ada mata kuliah</h3>
                    <p class="text-xs text-slate-400 max-w-sm mt-1 mb-4">Daftarkan mata kuliah semester ini untuk mengaktifkan jadwal di Kalender dan menghubungkannya dengan tugas harian.</p>
                    <button onclick="document.getElementById('courseModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-xs transition">
                        + Tambah Mata Kuliah Pertama
                    </button>
                </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Modal Add Course -->
    <div id="courseModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Tambah Mata Kuliah</h3>
                    <p class="text-xs text-slate-400">Masukkan jadwal & detail perkuliahanmu.</p>
                </div>
                <button onclick="document.getElementById('courseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form action="{{ route('courses.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kode MK</label>
                        <input type="text" 
                                name="course_code" 
                                placeholder="Contoh: 776" 
                                required 
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Mata Kuliah</label>
                        <input type="text" name="course_name" placeholder="Data Structures" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Hari Kuliah</label>
                        <select name="day" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                            <option value="Monday">Monday (Senin)</option>
                            <option value="Tuesday">Tuesday (Selasa)</option>
                            <option value="Wednesday">Wednesday (Rabu)</option>
                            <option value="Thursday">Thursday (Kamis)</option>
                            <option value="Friday">Friday (Jumat)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Waktu / Jam</label>
                        <input type="text" name="time" placeholder="09:00 - 10:30" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Dosen Pengampu</label>
                        <input type="text" name="lecturer_name" placeholder="Dr. Sarah Chen" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">SKS</label>
                        <input type="number" name="credits" value="3" min="1" max="6" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Ruangan / Lab</label>
                        <input type="text" name="room" placeholder="Engineering 112" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Warna Label</label>
                        <input type="color" name="color_hex" value="#4F46E5" class="w-full h-9 border border-slate-200 rounded-xl p-1 cursor-pointer">
                    </div>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('courseModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs">Simpan Course</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>