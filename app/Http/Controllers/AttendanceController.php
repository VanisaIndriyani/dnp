<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\AttendanceExport;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function createManual(Request $request)
    {
        $users = User::where('role', 'operator')->orderBy('name')->get();
        $selectedUser = $request->user_id;
        $selectedDate = $request->date;
        return view('admin.attendance.manual', compact('users', 'selectedUser', 'selectedDate'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_in' => 'nullable',
            'status' => 'required|in:present,sick,alpha,permission',
        ]);

        // Check for existing attendance
        $exists = Attendance::where('user_id', $request->user_id)
            ->whereDate('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Data absensi untuk user dan tanggal tersebut sudah ada.');
        }

        // Handle time formatting (append date to time)
        $date = $request->date;
        $timeIn = $request->time_in ? $date . ' ' . $request->time_in : null;

        Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'time_in' => $timeIn,
            'status' => $request->status,
            'is_approved' => true, // Manual entry by admin is auto-approved
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route(auth()->user()->role . '.attendance.index')->with('success', 'Absensi manual berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.attendance.edit', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable',
            'status' => 'required|in:present,sick,alpha,permission',
        ]);

        $date = $request->date;
        // If time_in is provided, format it. If empty (e.g. for alpha/sick), keep it null or set to null
        $timeIn = $request->time_in ? $date . ' ' . $request->time_in : null;

        // If status is alpha/sick/permission, time_in might be cleared if user wants
        // But let's respect the input.

        $attendance->update([
            'date' => $request->date,
            'time_in' => $timeIn,
            'status' => $request->status,
        ]);

        return redirect()->route(auth()->user()->role . '.attendance.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function create()
    {
        $user = auth()->user();
        
        // Load today's attendance for the authenticated user
        $user->load(['attendances' => function($q) {
            $q->whereDate('date', Carbon::today());
        }]);
        $user->today_attendance = $user->attendances->first();

        // Pass as a collection to reuse the view structure
        $users = collect([$user]);
        $division = $user->division;

        return view('operator.attendance.create', compact('users', 'division'));
    }

    public function store(Request $request)
    {
        // Support for "Quick Attendance" style (list of users)
        if ($request->has('nik')) {
            $request->validate([
                'nik' => 'required|exists:users,nik',
                'division' => 'required|in:case,cover,inner,endplate',
                'action' => 'nullable|in:present,absent',
            ]);

            $user = User::where('nik', $request->nik)->firstOrFail();

            // Update user division if different
            if ($user->division !== $request->division) {
                $user->update(['division' => $request->division]);
            }

            $today = Carbon::today();
            $now = Carbon::now();

            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance) {
                $status = ($request->action == 'absent') ? 'alpha' : $this->determineStatus($now);
                
                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'time_in' => $now,
                    'status' => $status,
                    'is_approved' => false,
                ]);
                
                $msg = ($status == 'alpha') ? 'Ditandai Tidak Hadir.' : 'Berhasil Absen Masuk!';
                return back()->with('success', $msg);
            } else {
                return back()->with('info', 'Anda sudah melakukan absensi hari ini.');
            }
        }

        // Fallback for self-attendance (legacy/single user mode)
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$todayAttendance) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::today(),
                'time_in' => Carbon::now(),
                'status' => 'present',
                'is_approved' => false,
            ]);
            return redirect()->route('operator.attendance.create')->with('success', 'Berhasil Absen Masuk.');
        } else {
            return redirect()->route('operator.attendance.create')->with('info', 'Anda sudah melakukan absensi hari ini.');
        }
    }

    private function determineStatus($time)
    {
        return 'present';
    }

    public function index(Request $request)
    {
        // Handle "Tidak Hadir" filter (Users without attendance OR with non-present status)
        if ($request->status == 'tidak_hadir') {
            $date = $request->date ?? Carbon::today()->toDateString();
            
            $query = \App\Models\User::where('role', 'operator')
                ->where(function($q) use ($date) {
                    // Case 1: No attendance record at all
                    $q->whereDoesntHave('attendances', function ($subQ) use ($date) {
                        $subQ->whereDate('date', $date);
                    })
                    // Case 2: Has attendance but status is NOT 'present' (e.g. alpha, sick)
                    ->orWhereHas('attendances', function ($subQ) use ($date) {
                        $subQ->whereDate('date', $date)
                             ->where('status', '!=', 'present');
                    });
                });

            // Filter by Division
            if ($request->has('division') && $request->division != '') {
                $query->where('division', $request->division);
            }

            // Paginate Users but pass them as "attendances" to the view
            // The view must handle that these are User objects
            $attendances = $query->orderBy('name')->paginate(10);
            
            // Append the date to each user object so the view knows which date we are talking about
            $attendances->getCollection()->transform(function ($user) use ($date) {
                $user->missing_date = $date;
                return $user;
            });
        } else {
            // Normal Attendance Query
            $query = Attendance::with('user');

            // If user is 'admin' (not super_admin), only show 'operator' attendance
            if (auth()->user()->role == 'admin') {
                $query->whereHas('user', function ($q) {
                    $q->where('role', 'operator');
                });
            }
            // If user is 'operator', only show their own attendance
            elseif (auth()->user()->role == 'operator') {
                $query->where('user_id', auth()->id());
            }

            // Filter by Date
            if ($request->has('date') && $request->date != '') {
                $query->whereDate('date', $request->date);
            }

            // Filter by Division
            if ($request->has('division') && $request->division != '') {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('division', $request->division);
                });
            }

            // Filter by Status (present, late, sick, etc.)
            // Note: 'hadir' filter is implicit here as we are querying Attendance model
            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'hadir') {
                     $query->where('status', 'present');
                } else {
                     $query->where('status', $request->status);
                }
            }

            $attendances = $query->latest()->paginate(10);
        }

        if (auth()->user()->role == 'super_admin') {
            return view('super_admin.attendance.index', compact('attendances'));
        } elseif (auth()->user()->role == 'operator') {
            return view('operator.attendance.index', compact('attendances'));
        }

        return view('admin.attendance.index', compact('attendances'));
    }

    public function approve(Attendance $attendance)
    {
        $attendance->update([
            'is_approved' => true,
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Absensi berhasil disetujui.');
    }

    public function export(Request $request)
    {
        $fileName = 'Data_Absensi_' . date('Y-m-d_H-i-s') . '.xlsx';
        $attendances = Attendance::with('user');

        // Apply role-based restrictions
        if (auth()->user()->role == 'admin') {
            $attendances->whereHas('user', function ($q) {
                $q->where('role', 'operator');
            });
        } elseif (auth()->user()->role == 'operator') {
            $attendances->where('user_id', auth()->id());
        }

        if ($request->has('date') && $request->date != '') {
            $attendances->whereDate('date', $request->date);
        }

        if ($request->has('division') && $request->division != '') {
            $attendances->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        // Apply sorting
        $attendances->orderBy('date', 'desc')->orderBy('time_in', 'asc');

        $data = $attendances->get();
        $date = $request->date ?? null;

        return Excel::download(new AttendanceExport($data, $date), $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new AttendanceImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data absensi berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray {
            public function array(): array
            {
                return [
                    ['nik', 'date', 'time_in', 'time_out', 'status'],
                    ['123456', '2026-02-12', '08:00', '17:00', 'hadir'],
                ];
            }
        }, 'template_absensi.xlsx');
    }

}
