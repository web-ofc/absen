@extends('layouts.master')

@section('title', 'Detail User - ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user me-2"></i>Detail User
                </h4>
                <div>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" 
                                 alt="{{ $user->name }}" 
                                 class="img-fluid rounded-circle mb-3"
                                 style="width: 200px; height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                 style="width: 200px; height: 200px;">
                                <i class="fas fa-user fa-5x text-white"></i>
                            </div>
                        @endif
                        
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->position ?? 'Tidak ada jabatan' }}</p>
                        
                        @if($user->is_active)
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle me-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%" class="fw-bold">NIP</td>
                                <td>: {{ $user->nip }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Email</td>
                                <td>: {{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jabatan</td>
                                <td>: {{ $user->position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status</td>
                                <td>: 
                                    @if($user->is_active)
                                        <span class="text-success">Aktif</span>
                                    @else
                                        <span class="text-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Data Wajah</td>
                                <td>: 
                                    @if($user->face_descriptor)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Tersedia
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fas fa-times-circle me-1"></i>Tidak tersedia
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Terdaftar</td>
                                <td>: {{ $user->created_at->format('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Terakhir Diupdate</td>
                                <td>: {{ $user->updated_at->format('d F Y, H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection