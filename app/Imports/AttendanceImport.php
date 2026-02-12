<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AttendanceImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nik = trim($row['nik']);
        $user = User::where('nik', $nik)->first();

        if (!$user) {
            return null; // Skip if user not found
        }

        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d');
        
        // Handle time_in
        $timeIn = null;
        if (!empty($row['time_in'])) {
            // Check if time_in is a float (Excel time) or string
            if (is_numeric($row['time_in'])) {
                $timeInObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_in']);
                $timeIn = $date . ' ' . $timeInObj->format('H:i:s');
            } else {
                $timeIn = $date . ' ' . $row['time_in'];
            }
        }

        // Handle time_out
        $timeOut = null;
        if (!empty($row['time_out'])) {
            if (is_numeric($row['time_out'])) {
                $timeOutObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_out']);
                $timeOut = $date . ' ' . $timeOutObj->format('H:i:s');
            } else {
                $timeOut = $date . ' ' . $row['time_out'];
            }
        }
        
        // Normalize status
        $status = strtolower(trim($row['status']));
        $validStatuses = ['present', 'sick', 'permission', 'alpha', 'late'];
        // Map Indonesian to English if needed
        $statusMap = [
            'hadir' => 'present',
            'sakit' => 'sick',
            'izin' => 'permission',
            'alpa' => 'alpha',
            'alpha' => 'alpha',
            'terlambat' => 'late'
        ];
        
        if (isset($statusMap[$status])) {
            $status = $statusMap[$status];
        }

        if (!in_array($status, $validStatuses)) {
            $status = 'present'; // Default
        }

        // Check if attendance already exists
        $exists = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return null; // Skip if exists
        }

        return new Attendance([
            'user_id' => $user->id,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => $status,
            'is_approved' => true,
            'approved_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'date' => 'required',
            'status' => 'required',
        ];
    }
}
