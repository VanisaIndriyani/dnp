<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $division = $request->query('division');
        $search = $request->query('search');
        $users = [];

        if ($division && in_array($division, ['case', 'cover', 'inner', 'endplate'])) {
            $query = User::where('division', $division);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            $users = $query->with(['attendances' => function($q) {
                    $q->whereDate('date', Carbon::today());
                }])
                ->get()
                ->map(function ($user) {
                    $user->today_attendance = $user->attendances->first();
                    return $user;
                });
        }

        return view('attendance.quick', compact('users', 'division'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:users,nik',
            'division' => 'required|in:case,cover,inner,endplate',
            'action' => 'nullable|in:present,absent', // Added action parameter
        ]);

        $user = User::where('nik', $request->nik)->firstOrFail();
        
        // Update user division if different from current
        if ($user->division !== $request->division) {
            $user->update(['division' => $request->division]);
        }

        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            // Check specific action or default to clock in
            $status = ($request->action == 'absent') ? 'alpha' : $this->determineStatus($now);
            
            // Clock In / Create Attendance Record
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'time_in' => $now,
                'status' => $status,
                'is_approved' => false,
            ]);
            
            $msg = ($status == 'alpha') ? 'Ditandai Tidak Hadir.' : 'Berhasil Absen Masuk!';
            return back()->with('success', $msg);
        } elseif (!$attendance->time_out && $request->action != 'absent') {
            // Clock Out
            $attendance->update([
                'time_out' => $now,
            ]);

            return back()->with('success', 'Berhasil Absen Pulang! Terima kasih, ' . $user->name);
        } else {
            // Already completed
            return back()->with('info', 'Anda sudah melakukan absensi hari ini.');
        }
    }

    private function determineStatus($time)
    {
        // Always return present as per requirement
        return 'present';
    }
}
