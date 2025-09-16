@extends('layouts.master')

@section('content')
<div class="page-title d-flex flex-column justify-content-center me-3 mb-4">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
        Role Details: {{ $role->name }}
    </h1>
</div>

<!-- Back Button -->
<div class="d-flex justify-content-start mb-5">
    <a href="{{ route('roles.index') }}" class="btn btn-light">
        <i class="ki-outline ki-arrow-left fs-2"></i>Back to Roles
    </a>
</div>

<div class="row">
    <!-- Role Info Card -->
    <div class="col-xl-6">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2>Role Information</h2>
                </div>
            </div>
            <div class="card-body pt-1">
                <div class="fw-bold text-gray-600 mb-5">
                    <div class="d-flex align-items-center py-2">
                        <span class="fw-bold text-gray-800 me-2">Role Name:</span>
                        <span class="badge badge-light-primary fs-7">{{ $role->name }}</span>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="fw-bold text-gray-800 me-2">Total Users:</span>
                        <span class="badge badge-light-info fs-7">{{ $role->users->count() }} Users</span>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="fw-bold text-gray-800 me-2">Total Permissions:</span>
                        <span class="badge badge-light-success fs-7">{{ $role->permissions->count() }} Permissions</span>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="fw-bold text-gray-800 me-2">Created:</span>
                        <span class="text-gray-600">{{ $role->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Card -->
    <div class="col-xl-6">
        <div class="card card-flush h-lg-100">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2>Permissions</h2>
                </div>
            </div>
            <div class="card-body pt-1">
                <div class="d-flex flex-column text-gray-600">
                    @forelse($role->permissions as $permission)
                        <div class="d-flex align-items-center py-2">
                            <i class="ki-outline ki-check text-success fs-4 me-3"></i>
                            <span class="fw-semibold">{{ $permission->name }}</span>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="ki-outline ki-information fs-3x text-muted mb-5"></i>
                            <div class="text-muted fw-bold fs-5">No permissions assigned to this role</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users with this Role -->
@if($role->users->count() > 0)
<div class="row mt-5">
    <div class="col-12">
        <div class="card card-flush">
            <div class="card-header pt-7">
                <div class="card-title">
                    <h2>Users with this Role</h2>
                </div>
            </div>
            <div class="card-body pt-1">
                <div class="table-responsive">
                    <table class="table table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach($role->users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                            <div class="symbol-label">
                                                <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-light-success">Active</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection