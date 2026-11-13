<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        // Hanya ambil matkul milik user yang sedang login
        $courses = Course::where('user_id', Auth::id())
            ->with('tasks')
            ->get();

        return view('courses', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code'   => 'required|numeric|digits_between:1,20',
            'course_name'   => 'required|string|max:255',
            'lecturer_name' => 'required|string|max:255',
            'room'          => 'nullable|string|max:100',
            'day'           => 'required|string',
            'time'          => 'required|string',
            'credits'       => 'required|integer|min:1|max:6',
            'color_hex'     => 'required|string|max:10',
        ]);

        // Gabungkan day dan time ke kolom schedule_time (contoh: "Monday · 09:00 - 10:30")
        $scheduleTime = $validated['day'] . ' · ' . $validated['time'];

        Course::create([
            'user_id'       => Auth::id(),
            'course_code'   => $validated['course_code'],
            'course_name'   => $validated['course_name'],
            'lecturer_name' => $validated['lecturer_name'],
            'room'          => $validated['room'] ?? 'Online / Lab',
            'schedule_time' => $scheduleTime,
            'credits'       => $validated['credits'],
            'color_hex'     => $validated['color_hex'],
        ]);

        return redirect()->route('courses.index')->with('success', 'Course added successfully!');
    }

    public function calendar()
    {
        // Hanya ambil matkul dan tugas milik user yang sedang login
        $courses = Course::where('user_id', Auth::id())->get();
        $tasks = Task::where('user_id', Auth::id())
            ->with('course')
            ->orderBy('due_date')
            ->get();

        return view('calendar', compact('courses', 'tasks'));
    }

    public function focus()
    {
        // Hanya ambil tugas aktif milik user yang sedang login
        $tasks = Task::where('user_id', Auth::id())
            ->where('status', '!=', 'done')
            ->get();

        return view('focus', compact('tasks'));
    }
}