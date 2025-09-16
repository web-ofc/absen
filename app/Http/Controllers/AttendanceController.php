<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Geozone;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\FaceRecognitionService;

class AttendanceController extends Controller
{
    

// public function store(Request $request)
// {
//     $request->validate([
//         'name'  => 'required|string',
//         'photo' => 'required|string',
//     ]);

//     $user = User::where('name', $request->name)->first();
//     if (!$user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }

//     // cek apakah sudah absen hari ini
//     $already = Attendance::where('user_id', $user->id)
//         ->whereDate('date', today()) // pakai kolom date, bukan created_at
//         ->exists();

//     if ($already) {
//         return response()->json(['message' => 'Sudah absen hari ini']);
//     }

//     // simpan photo base64 → file
//     $image = $request->photo;
//     $image = str_replace('data:image/jpeg;base64,', '', $image);
//     $image = str_replace(' ', '+', $image);
//     $imageName = 'attendance/' . uniqid() . '.jpg';
//     Storage::disk('public')->put($imageName, base64_decode($image));

//     // simpan absensi
//     $attendance = Attendance::create([
//         'user_id' => $user->id,
//         'date'    => Carbon::now('Asia/Jakarta')->toDateString(),
//         'time_in' => Carbon::now('Asia/Jakarta')->toTimeString(),
//         'photo'   => $imageName,
//     ]);
//     return response()->json([
//         'message' => 'Absensi berhasil!',
//         'attendance' => $attendance
//     ]);
// }

    // private function distance($lat1, $lon1, $lat2, $lon2)
    // {
    //     $earthRadius = 6371000; // meter
    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLon = deg2rad($lon2 - $lon1);
    //     $a = sin($dLat/2) * sin($dLat/2) +
    //          cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
    //          sin($dLon/2) * sin($dLon/2);
    //     $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    //     return $earthRadius * $c;
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'      => 'required|string',
    //         'photo'     => 'required|string',
    //         'type'      => 'required|string|in:in,out',
    //         'latitude'  => 'nullable|numeric',
    //         'longitude' => 'nullable|numeric',
    //     ]);

    //     // cari user
    //     $user = User::where('name', $request->name)->first();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $userLat = $request->latitude;
    //     $userLon = $request->longitude;

    //     // cek apakah dalam radius salah satu geozone
    //     $zones = Geozone::where('is_active', true)->get();
    //     $validZone = null;

    //     foreach ($zones as $zone) {
    //         $dist = $this->distance($userLat, $userLon, $zone->latitude, $zone->longitude);
    //         if ($dist <= $zone->radius) {
    //             $validZone = $zone;
    //             break;
    //         }
    //     }

    //     // simpan photo base64 → file
    //     $image = $request->photo;
    //     $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
    //     $image = str_replace(' ', '+', $image);
    //     $imageName = 'attendance/' . uniqid() . '.jpg';
    //     Storage::disk('public')->put($imageName, base64_decode($image));

    //     // ambil waktu server (Asia/Jakarta)
    //     $serverTime = Carbon::now('Asia/Jakarta');
    //     $today      = $serverTime->toDateString();

    //     // cek apakah sudah ada absen hari ini
    //     $attendance = Attendance::where('user_id', $user->id)
    //         ->where('date', $today)
    //         ->first();

    //     if ($request->type === 'in') {
    //         if ($attendance) {
    //             return response()->json(['message' => 'Sudah absen masuk hari ini']);
    //         }

    //         $attendance = Attendance::create([
    //             'user_id'    => $user->id,
    //             'date'       => $today,
    //             'time_in'    => $serverTime->format('H:i:s'),
    //             'photo'      => $imageName,
    //             'geozone_id' => $validZone ? $validZone->id : null,
    //         ]);
    //     } elseif ($request->type === 'out') {
    //         if (!$attendance) {
    //             return response()->json(['message' => 'Belum absen masuk']);
    //         }
    //         if ($attendance->time_out) {
    //             return response()->json(['message' => 'Sudah absen pulang hari ini']);
    //         }

    //         $attendance->update([
    //             'time_out'   => $serverTime->format('H:i:s'),
    //             'photo'      => $imageName,
    //             'geozone_id' => $validZone ? $validZone->id : null,
    //         ]);
    //     }

    //     return response()->json([
    //         'message'    => 'Absensi berhasil dicatat!',
    //         'attendance' => $attendance,
    //         'geozone'    => $validZone ? $validZone->name : null,
    //     ]);
    // }

    // private function distance($lat1, $lon1, $lat2, $lon2)
    // {
    //     $earthRadius = 6371000; // meter
    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLon = deg2rad($lon2 - $lon1);
    //     $a = sin($dLat/2) * sin($dLat/2) +
    //         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
    //         sin($dLon/2) * sin($dLon/2);
    //     $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    //     return $earthRadius * $c;
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'      => 'required|string',
    //         'photo'     => 'required|string',
    //         'type'      => 'required|string|in:in,out',
    //         'latitude'  => 'nullable|numeric',
    //         'longitude' => 'nullable|numeric',
    //     ]);

    //     // cari user
    //     $user = User::where('name', $request->name)->first();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $userLat = $request->latitude;
    //     $userLon = $request->longitude;

    //     // cek apakah dalam radius salah satu geozone
    //     $zones = Geozone::where('is_active', true)->get();
    //     $validZone = null;

    //     foreach ($zones as $zone) {
    //         $dist = $this->distance($userLat, $userLon, $zone->latitude, $zone->longitude);
    //         if ($dist <= $zone->radius) {
    //             $validZone = $zone;
    //             break;
    //         }
    //     }

    //     // simpan photo base64 → file
    //     $image = $request->photo;
    //     $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
    //     $image = str_replace(' ', '+', $image);
        
    //     // ambil waktu server (Asia/Jakarta)
    //     $serverTime = Carbon::now('Asia/Jakarta');
    //     $today = $serverTime->toDateString();

    //     // cek apakah sudah ada absen hari ini
    //     $attendance = Attendance::where('user_id', $user->id)
    //         ->whereDate('created_at', $today)
    //         ->first();

    //     if ($request->type === 'in') {
    //         if ($attendance) {
    //             return response()->json(['message' => 'Sudah absen masuk hari ini']);
    //         }

    //         $imageName = 'attendance/in/' . uniqid() . '.jpg';
    //         Storage::disk('public')->put($imageName, base64_decode($image));

    //         $attendance = Attendance::create([
    //             'user_id'        => $user->id,
    //             'time_in'        => $serverTime,
    //             'photo_in'       => $imageName,
    //             'check_in_lat'   => $userLat,
    //             'check_in_lng'   => $userLon,
    //         ]);
    //     } elseif ($request->type === 'out') {
    //         if (!$attendance) {
    //             return response()->json(['message' => 'Belum absen masuk']);
    //         }
    //         if ($attendance->time_out) {
    //             return response()->json(['message' => 'Sudah absen pulang hari ini']);
    //         }

    //         $imageName = 'attendance/out/' . uniqid() . '.jpg';
    //         Storage::disk('public')->put($imageName, base64_decode($image));

    //         $attendance->update([
    //             'time_out'       => $serverTime,
    //             'photo_out'      => $imageName,
    //             'check_out_lat'  => $userLat,
    //             'check_out_lng'  => $userLon,
    //         ]);
    //     }

    //     return response()->json([
    //         'message'    => 'Absensi berhasil dicatat!',
    //         'attendance' => $attendance,
    //         'geozone'    => $validZone ? $validZone->name : null,
    //     ]);
    // }

    
    private function distance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371000; // meter
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

public function store(Request $request)
{
    $user = $request->user();
    $serverTime = Carbon::now();
    $image = $request->photo;
    $userLat = $request->latitude ?? null;
    $userLon = $request->longitude ?? null;

    // === Validasi lokasi (jika GPS dikirim) ===
    if ($userLat && $userLon) {
        $canAttend = false;

        if ($user->can_attend_anywhere) {
            $canAttend = true;
        } else {
            $geozones = $user->geozones()->where('is_active', true)->get();

            foreach ($geozones as $zone) {
                $distance = $this->distance($userLat, $userLon, $zone->latitude, $zone->longitude);
                if ($distance <= $zone->radius) {
                    $canAttend = true;
                    break;
                }
            }
        }

        if (!$canAttend) {
            return response()->json([
                'message' => 'Anda berada di luar area absen yang diizinkan.'
            ], 422);
        }
    }

    $lastAttendance = Attendance::where('user_id', $user->id)
        ->latest()
        ->first();

   // ======== ABSEN MASUK ========
    if ($request->type === 'in') {
        if ($lastAttendance && !$lastAttendance->time_out) {
            return response()->json(['message' => 'Anda belum checkout dari absen sebelumnya.'], 422);
        }


        $imageName = 'attendance/in/' . uniqid() . '.jpg';
        Storage::disk('public')->put($imageName, base64_decode($image));

        $attendance = Attendance::create([
            'user_id'      => $user->id,
            'time_in'      => $serverTime,
            'photo_in'     => $imageName,
            'check_in_lat' => $userLat,
            'check_in_lng' => $userLon,
        ]);

        return response()->json(['message' => 'Berhasil absen masuk!', 'attendance' => $attendance]);
    }


    // ======== ABSEN PULANG ========
    if ($request->type === 'out') {
        if (!$lastAttendance) {
            return response()->json(['message' => 'Belum ada absen masuk'], 422);
        }

        if ($lastAttendance->time_out) {
            return response()->json(['message' => 'Sudah absen pulang'], 422);
        }

        $canCheckout = false;

        if ($user->can_anytime) {
            // aturan minimal 9 jam
            $jamKerja = Carbon::parse($lastAttendance->time_in)->diffInHours($serverTime);
            if ($jamKerja >= 9) {
                $canCheckout = true;
            }
        } elseif ($user->workSchedule) {
            $schedule = $user->workSchedule;
            $timeIn   = Carbon::parse($lastAttendance->time_in);

            $scheduleStart = Carbon::parse($timeIn->format('Y-m-d') . ' ' . $schedule->start_time);
            $scheduleEnd   = Carbon::parse($timeIn->format('Y-m-d') . ' ' . $schedule->end_time);

            if ($schedule->cross_midnight) {
                $scheduleEnd->addDay($schedule->end_time_next_day);
            }

            if ($serverTime->greaterThanOrEqualTo($scheduleEnd)) {
                $canCheckout = true;
            }
        }

        if (!$canCheckout) {
            return response()->json(['message' => 'Belum bisa checkout sesuai aturan jadwal kerja.'], 422);
        }

        $imageName = 'attendance/out/' . uniqid() . '.jpg';
        Storage::disk('public')->put($imageName, base64_decode($image));

        $lastAttendance->update([
            'time_out'      => $serverTime,
            'photo_out'     => $imageName,
            'check_out_lat' => $userLat,
            'check_out_lng' => $userLon,
        ]);

        return response()->json(['message' => 'Berhasil absen pulang!', 'attendance' => $lastAttendance]);
    }
}

    

}
