<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tasks = $user->tasks()->with('course')->orderBy('due_date', 'asc')->get();

        // Kelompokkan gaya Google Tasks: Aktif (todo + in_progress) vs Selesai (done)
        $activeTasks = $tasks->whereIn('status', ['todo', 'in_progress']);
        $doneTasks   = $tasks->where('status', 'done');

        // Tetap kirim variabel lama agar kompatibel jika masih dipakai bagian lain
        $todoTasks       = $tasks->where('status', 'todo');
        $inProgressTasks = $tasks->where('status', 'in_progress');

        $courses = Course::where('user_id', $user->id)->get();

        return view('dashboard', compact(
            'tasks', 'activeTasks', 'todoTasks', 'inProgressTasks', 'doneTasks', 'courses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'course_id'   => 'required|exists:courses,id',
            'due_date'    => 'required|date',
            'priority'    => 'nullable|in:low,medium,high',
            'description' => 'nullable|string',
        ]);

        $request->user()->tasks()->create([
            'title'       => $validated['title'],
            'course_id'   => $validated['course_id'],
            'due_date'    => $validated['due_date'],
            'priority'    => $validated['priority'] ?? 'medium',
            'description' => $validated['description'] ?? null,
            'status'      => 'todo',
        ]);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'course_id'   => 'required|exists:courses,id',
            'due_date'    => 'required|date',
            'priority'    => 'nullable|in:low,medium,high',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:todo,in_progress,done',
        ]);

        $task->update([
            'title'       => $validated['title'],
            'course_id'   => $validated['course_id'],
            'due_date'    => $validated['due_date'],
            'priority'    => $validated['priority'] ?? $task->priority,
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? $task->status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->route('dashboard')->with('success', 'Status tugas diperbarui!');
    }

    public function destroy(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');

        $task->delete();

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil dihapus!');
    }

    public function aiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $apiKey = env('GEMINI_API_KEY') ?: config('services.gemini.key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'reply'  => 'Gemini API Key belum terdeteksi di .env!',
                'time'   => now()->format('H:i')
            ]);
        }

        $activeTasks = $user->tasks()->whereIn('status', ['todo', 'in_progress'])->with('course')->get();
        $doneTasks   = $user->tasks()->where('status', 'done')->get();
        $courses     = Course::where('user_id', $user->id)->get();

        $taskSummary = "Daftar Tugas Aktif:\n";
        foreach ($activeTasks as $t) {
            $taskSummary .= "- [ID:{$t->id}] {$t->title} (MK: " . ($t->course->course_name ?? '-') . ", Deadline: {$t->due_date}, Prioritas: {$t->priority})\n";
        }
        $taskSummary .= "Tugas Selesai: " . $doneTasks->count() . " tugas.\n";

        $courseSummary = "Mata Kuliah Terdaftar (gunakan ID ini jika menambah tugas):\n";
        foreach ($courses as $c) {
            $courseSummary .= "- [ID:{$c->id}] Kode:{$c->course_code} | Nama:{$c->course_name} | Hari:{$c->day} | Jam:{$c->schedule_time}\n";
        }

        $today = now()->format('Y-m-d');

        $systemInstruction = "Kamu adalah Study Assistant cerdas di aplikasi StudyFlow untuk mahasiswa bernama {$user->name}. Hari ini tanggal {$today}.\n"
            . "Selain menjawab pertanyaan, kamu BISA langsung mencatat Tugas atau Mata Kuliah baru ke database jika diminta user.\n\n"
            . "ATURAN EKSEKUSI DATABASE:\n"
            . "1. Jika user meminta mencatat/menambah TUGAS baru, sertakan blok JSON persis di akhir balasanmu dengan format:\n"
            . "[ACTION]{\"type\":\"create_task\",\"title\":\"Judul Tugas\",\"course_id\":ID_MATKUL_DARI_DAFTAR,\"due_date\":\"YYYY-MM-DD\",\"priority\":\"low|medium|high\",\"description\":\"catatan singkat\"}[/ACTION]\n"
            . "   - Cocokkan course_id dari daftar Mata Kuliah Terdaftar di bawah. Jika tidak disebut atau tidak cocok, gunakan ID mata kuliah pertama yang tersedia.\n"
            . "   - Jika deadline tidak disebut user, isi default 3 hari dari {$today}.\n"
            . "2. Jika user meminta menambah MATA KULIAH baru, sertakan blok JSON persis di akhir balasanmu dengan format:\n"
            . "[ACTION]{\"type\":\"create_course\",\"course_code\":\"KODE_ANGKA\",\"course_name\":\"Nama Matkul\",\"lecturer_name\":\"Nama Dosen\",\"day\":\"Monday\",\"schedule_time\":\"08:00 - 10:00\",\"credits\":3,\"room\":\"-\"}[/ACTION]\n"
            . "3. Jika user hanya bertanya/diskusi biasa, JANGAN sertakan blok [ACTION].\n"
            . "4. Selalu berikan balasan teks ramah yang mengonfirmasi detail data yang dicatat.\n\n"
            . "Data akademik {$user->name}:\n{$courseSummary}\n{$taskSummary}";

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(20)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemInstruction . "\n\nPesan mahasiswa: " . $request->message]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $result    = $response->json();
                $replyText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, belum ada tanggapan.';
                $reload    = false;

                // Cek apakah Gemini menyertakan perintah [ACTION]...[/ACTION]
                if (preg_match('/\[ACTION\](.*?)\[\/ACTION\]/s', $replyText, $matches)) {
                    $actionData = json_decode(trim($matches[1]), true);
                    // Hapus tag [ACTION] dari teks balasan agar tidak tampil mentah di chat
                    $replyText  = trim(preg_replace('/\[ACTION\].*?\[\/ACTION\]/s', '', $replyText));

                    if (is_array($actionData) && isset($actionData['type'])) {
                        if ($actionData['type'] === 'create_task') {
                            $courseId = $actionData['course_id'] ?? ($courses->first()->id ?? null);
                            if ($courseId && Course::where('id', $courseId)->where('user_id', $user->id)->exists()) {
                                $user->tasks()->create([
                                    'title'       => $actionData['title'] ?? 'Tugas Baru',
                                    'course_id'   => $courseId,
                                    'due_date'    => $actionData['due_date'] ?? now()->addDays(3)->format('Y-m-d'),
                                    'priority'    => in_array($actionData['priority'] ?? '', ['low', 'medium', 'high']) ? $actionData['priority'] : 'medium',
                                    'description' => $actionData['description'] ?? 'Dicatat otomatis oleh Study Assistant',
                                    'status'      => 'todo',
                                ]);
                                $reload = true;
                            } else {
                                $replyText .= "\n\n⚠️ Catatan: Kamu belum punya mata kuliah terdaftar, tambahkan minimal 1 mata kuliah dulu ya agar tugas bisa dikaitkan.";
                            }
                        } elseif ($actionData['type'] === 'create_course') {
                            Course::create([
                                'user_id'       => $user->id,
                                'course_code'   => preg_replace('/[^0-9]/', '', (string)($actionData['course_code'] ?? rand(100, 999))),
                                'course_name'   => $actionData['course_name'] ?? 'Mata Kuliah Baru',
                                'lecturer_name' => $actionData['lecturer_name'] ?? 'Dosen Pengampu',
                                'day'           => $actionData['day'] ?? 'Monday',
                                'schedule_time' => $actionData['schedule_time'] ?? '08:00 - 10:00',
                                'credits'       => (int)($actionData['credits'] ?? 3),
                                'room'          => $actionData['room'] ?? '-',
                                'color_hex'     => '#4F46E5',
                            ]);
                            $reload = true;
                        }
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'reply'  => nl2br(e($replyText)),
                    'reload' => $reload,
                    'time'   => now()->format('H:i')
                ]);
            }

            $err = $response->json();
            return response()->json([
                'status' => 'error',
                'reply'  => 'Respon Gemini API: ' . ($err['error']['message'] ?? ('HTTP ' . $response->status())),
                'time'   => now()->format('H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'reply'  => 'Kendala Server: ' . $e->getMessage(),
                'time'   => now()->format('H:i')
            ]);
        }
    }
}