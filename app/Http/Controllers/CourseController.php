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

        Course::create([
            'user_id'       => Auth::id(),
            'course_code'   => $validated['course_code'],
            'course_name'   => $validated['course_name'],
            'lecturer_name' => $validated['lecturer_name'],
            'room'          => $validated['room'] ?? 'Online / Lab',
            'schedule_time' => $validated['day'] . ' · ' . $validated['time'],
            'credits'       => $validated['credits'],
            'color_hex'     => $validated['color_hex'],
        ]);

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    public function update(Request $request, Course $course)
    {
        abort_unless($course->user_id === Auth::id(), 403, 'Akses tidak diizinkan.');

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

        $course->update([
            'course_code'   => $validated['course_code'],
            'course_name'   => $validated['course_name'],
            'lecturer_name' => $validated['lecturer_name'],
            'room'          => $validated['room'] ?? 'Online / Lab',
            'schedule_time' => $validated['day'] . ' · ' . $validated['time'],
            'credits'       => $validated['credits'],
            'color_hex'     => $validated['color_hex'],
        ]);

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil diperbarui!');
    }

    public function destroy(Course $course)
    {
        abort_unless($course->user_id === Auth::id(), 403, 'Akses tidak diizinkan.');

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil dihapus!');
    }

    public function calendar()
    {
        $courses = Course::where('user_id', Auth::id())->get();
        $tasks = Task::where('user_id', Auth::id())
            ->with('course')
            ->orderBy('due_date')
            ->get();

        return view('calendar', compact('courses', 'tasks'));
    }

    public function focus()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('status', '!=', 'done')
            ->get();

        return view('focus', compact('tasks'));
    }
}