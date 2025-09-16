<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Geozone;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

     public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'can_attend_anywhere' => $user->can_attend_anywhere,
            'geozones' => $user->can_attend_anywhere
                ? \App\Models\Geozone::where('is_active', true)->get()
                : $user->geozones()->where('is_active', true)->get(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Gunakan eager loading untuk memuat roles
        $users = User::with('roles')->get();
        return view('pages.users.index', compact('users'));
    }

        public function create()
    {
        $geozones = Geozone::where('is_active', true)->get();
        $workSchedules = WorkSchedule::where('is_active', true)->get(); // Tambahkan ini
        $roles = Role::all(); 

        return view('pages.users.create', compact('geozones', 'workSchedules', 'roles'));
    }


        public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'face_descriptor' => 'required|string',
            'photo' => 'required|string',
            'geozones' => 'array|nullable',
            'can_attend_anywhere' => 'boolean',
            'can_anytime' => 'boolean', // Tambahkan validasi
            'work_schedule_id' => 'nullable|exists:work_schedules,id', // Tambahkan validasi
            'roles' => 'required|array|min:1', // Tambahkan validasi untuk roles
            'roles.*' => 'exists:roles,name', // Pastikan role yang dipilih ada di tabel roles
        ]);

        // Simpan foto
        $photoData = $request->photo;
        $photoName = 'user_' . time() . '_' . $request->name . '.jpg'; // Gunakan name bukan nip
        if (strpos($photoData, 'data:image') === 0) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
        }
        Storage::disk('public')->put('photos/' . $photoName, base64_decode($photoData));


        // LOGIC UTAMA: Atur work_schedule_id berdasarkan can_anytime
        $workScheduleId = null;
        if (!$request->boolean('can_anytime')) {
            // Jika tidak bisa absen kapan saja, wajib pilih work schedule
            $workScheduleId = $request->work_schedule_id;
            
            if (!$workScheduleId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Work schedule harus dipilih jika tidak bisa absen kapan saja'
                ], 422);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'face_descriptor' => $request->face_descriptor,
            'photo' => 'photos/' . $photoName,
            'can_attend_anywhere' => $request->boolean('can_attend_anywhere'),
            'can_anytime' => $request->boolean('can_anytime'), // Simpan can_anytime
            'work_schedule_id' => $workScheduleId // Simpan work_schedule_id
        ]);

        // Sync geozones
        if ($request->filled('geozones')) {
            $user->geozones()->sync($request->geozones);
        }

            // Kita menggunakan `syncRoles` untuk mengassign roles ke user
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['success' => true, 'message' => 'User berhasil didaftarkan!']);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|string|unique:users,nip,' . $id,
            'position' => 'nullable|string|max:255'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'position' => $request->position,
            'is_active' => $request->has('is_active')
        ];

        // Jika ada update face descriptor
        if ($request->filled('face_descriptor')) {
            $updateData['face_descriptor'] = $request->face_descriptor;
        }

        // Jika ada update foto
        if ($request->filled('photo')) {
            // Hapus foto lama
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Simpan foto baru
            $photoData = $request->photo;
            $photoName = 'user_' . time() . '_' . $request->nip . '.jpg';
            
            if (strpos($photoData, 'data:image') === 0) {
                $photoData = substr($photoData, strpos($photoData, ',') + 1);
            }
            
            Storage::disk('public')->put('photos/' . $photoName, base64_decode($photoData));
            $updateData['photo'] = 'photos/' . $photoName;
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate!']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Hapus foto jika ada
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus!']);
    }
}
