<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    //  public function __construct()
    // {
    //     // Pengecekan izin untuk melihat tabel roles
    //     Gate::authorize('read roles');
    // }
    /**
     * Display a listing of the resource.
     */
      public function index()
{
    Gate::authorize('read roles');
    // Ambil semua role dengan permissions
    $roles = Role::with('permissions')->withCount('users')->get();

    // Ambil semua permissions (buat checkbox di modal)
    $allPermissions = Permission::all();

    return view('pages.roles.index', compact('roles', 'allPermissions'));
}




    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        $allPermissions = Permission::all();
        return view('pages.roles.index', compact('allPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Gate::authorize('create roles');
        
        $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        try {
            // Buat role baru
            $role = Role::create([
                'name' => $request->role_name,
                'guard_name' => 'web'
            ]);

            // Assign permissions jika ada
            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }

            return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal membuat role: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
     public function show(string $id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        return view('pages.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
        public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPermissions = Permission::all();

        return view('pages.roles.index', compact('role', 'allPermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->role_name]);

        // sync permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
