<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'operator')->get();

        foreach ($users as $user) {
            // Today's attendance (Present)
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::today(),
                'time_in' => '07:55:00',
                'time_out' => '17:05:00',
                'status' => 'present',
            ]);

            // Yesterday's attendance (Present even if late time)
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::yesterday(),
                'time_in' => '08:15:00',
                'time_out' => '17:00:00',
                'status' => 'present',
            ]);

           
        }
    }
}
