{{-- File: resources/views/layouts/mobile-bottom-nav.blade.php --}}

<!-- Mobile Bottom Navigation - Sebagai fitur tambahan, tidak menggantikan sidebar -->
{{-- File: resources/views/layouts/mobile-bottom-nav.blade.php --}}

<div class="mobile-bottom-nav" id="mobile-bottom-nav">
    <div class="nav-container">
        <!-- Dashboard/Home -->
        <a href="{{ route('dashboard') }}" 
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="ki-outline ki-home-2 fs-2"></i>
            </div>
            <span class="nav-text">Home</span>
        </a>

        <!-- Recognition/Absensi -->
        <a href="{{ url('/recognition') }}" 
           class="nav-item {{ request()->is('recognition*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="ki-outline ki-home-2 fs-2"></i>
            </div>
            <span class="nav-text">Scan</span>
        </a>

        <!-- Users Management -->
        <a href="{{ route('users.index') }}" 
           class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <div class="nav-icon">
                <i class="ki-outline ki-home-2 fs-2"></i>
            </div>
            <span class="nav-text">Users</span>
        </a>


        <!-- Profile/Account -->
        <a href="#" 
           class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" 
           data-bs-toggle="modal" 
           data-bs-target="#kt_modal_user_profile">
            <div class="nav-icon">
                <i class="ki-outline ki-home-2 fs-2"></i>
            </div>
            <span class="nav-text">Profile</span>
        </a>
    </div>
</div>


{{-- Modal Profile untuk mobile --}}
<div class="modal fade" id="kt_modal_user_profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">User Profile</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-60px me-5">
                        <img alt="Logo" src="{{ asset('assets/media/avatars/300-3.jpg') }}" />
                    </div>
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-4">
                            {{ Auth::user()->nama }}
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{ Auth::user()->role }}</span>
                        </div>
                        <a href="#" class="fw-semibold text-muted text-hover-primary fs-6">
                            {{ Auth::user()->username }}
                        </a>
                    </div>
                </div>
                <div class="separator my-4"></div>
                
                <!-- Menu items untuk profile -->
                <div class="d-flex flex-column">
                    <a href="#" class="btn btn-light-primary btn-active-primary mb-3">
                        <i class="ki-outline ki-user fs-2 me-3"></i>
                        Edit Profile
                    </a>
                    <a href="#" class="btn btn-light-info btn-active-info mb-3">
                        <i class="ki-outline ki-setting-2 fs-2 me-3"></i>
                        Settings
                    </a>
                    <form action="/logout" method="post" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light-danger btn-active-danger w-100">
                            <i class="ki-outline ki-exit-right fs-2 me-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>