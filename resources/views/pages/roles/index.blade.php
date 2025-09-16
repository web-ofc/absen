@extends('layouts.master')

@section('content')
<div class="page-title d-flex flex-column justify-content-center me-3 mb-4">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Roles List</h1>
</div>

<!-- Alert Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Add Role Button -->
<div class="d-flex justify-content-end mb-5">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_role">
        <i class="ki-outline ki-plus fs-2"></i>Add New Role
    </button>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
    @foreach($roles as $role)
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <!-- Card header -->
                <div class="card-header">
                    <div class="card-title">
                        <h2>{{ $role->name }}</h2>
                    </div>
                </div>

                <!-- Card body -->
                <div class="card-body pt-1">
                    <div class="fw-bold text-gray-600 mb-5">
                        Total users with this role: {{ $role->users_count }}
                    </div>

                    <!-- Permissions -->
                    <div class="d-flex flex-column text-gray-600">
                        @forelse($role->permissions as $permission)
                            <div class="d-flex align-items-center py-2">
                                <span class="bullet bg-primary me-3"></span>{{ $permission->name }}
                            </div>
                        @empty
                            <div class="d-flex align-items-center py-2 text-muted">
                                <em>No permissions assigned</em>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Card footer -->
                <div class="card-footer flex-wrap pt-0">
                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                    <button type="button" 
                        class="btn btn-light btn-active-light-primary my-1 me-2 btn-edit-role" 
                        data-id="{{ $role->id }}"
                        data-name="{{ $role->name }}"
                        data-permissions='@json($role->permissions->pluck("name"))'
                        data-bs-toggle="modal" 
                        data-bs-target="#kt_modal_update_role">
                        Edit Role
                    </button>

                    @php
                        $protectedRoles = ['super-admin', 'admin', 'administrator'];
                        $canDelete = !in_array(strtolower($role->name), $protectedRoles) && $role->users_count == 0;
                    @endphp

                    @if($canDelete)
                    <button type="button" 
                        class="btn btn-light-danger btn-active-danger my-1 btn-delete-role" 
                        data-id="{{ $role->id }}"
                        data-name="{{ $role->name }}">
                        Delete
                    </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="kt_modal_create_role" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-750px">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="fw-bold">Create New Role</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <i class="ki-outline ki-cross fs-1"></i>
        </div>
      </div>
      <div class="modal-body scroll-y mx-5 my-7">
        <form id="kt_modal_create_role_form" method="POST" action="{{ route('roles.store') }}">
          @csrf

          <div class="fv-row mb-10">
            <label class="fs-5 fw-bold form-label mb-2">Role name</label>
            <input class="form-control form-control-solid" name="role_name" placeholder="Enter role name" required />
          </div>

          <div class="fv-row">
            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
            <div class="table-responsive">
              <table class="table align-middle table-row-dashed fs-6 gy-5">
                <tbody class="text-gray-600 fw-semibold">
                  @foreach($allPermissions as $permission)
                  <tr>
                    <td class="text-gray-800">{{ $permission->name }}</td>
                    <td>
                      <label class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input create-permission" type="checkbox" 
                          name="permissions[]" value="{{ $permission->name }}">
                        <span class="form-check-label">Allow</span>
                      </label>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="text-center pt-15">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <span class="indicator-label">Create Role</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Update Role Modal -->
<div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-750px">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="fw-bold">Update Role</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <i class="ki-outline ki-cross fs-1"></i>
        </div>
      </div>
      <div class="modal-body scroll-y mx-5 my-7">
        <form id="kt_modal_update_role_form" method="POST">
          @csrf
          @method('PUT')

          <input type="hidden" name="role_id" id="edit_role_id">

          <div class="fv-row mb-10">
            <label class="fs-5 fw-bold form-label mb-2">Role name</label>
            <input class="form-control form-control-solid" name="role_name" id="edit_role_name" />
          </div>

          <div class="fv-row">
            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
            <div class="table-responsive">
              <table class="table align-middle table-row-dashed fs-6 gy-5">
                <tbody class="text-gray-600 fw-semibold">
                  @foreach($allPermissions as $permission)
                  <tr>
                    <td class="text-gray-800">{{ $permission->name }}</td>
                    <td>
                      <label class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input edit-permission" type="checkbox" 
                          name="permissions[]" value="{{ $permission->name }}">
                        <span class="form-check-label">Allow</span>
                      </label>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="text-center pt-15">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <span class="indicator-label">Update Role</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="kt_modal_delete_role" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-450px">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="fw-bold text-danger">Confirm Delete</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
          <i class="ki-outline ki-cross fs-1"></i>
        </div>
      </div>
      <div class="modal-body text-center py-10">
        <i class="ki-outline ki-warning fs-3x text-danger mb-5"></i>
        <p class="fw-bold fs-4 text-gray-700 mb-5">
          Are you sure you want to delete role "<span id="delete_role_name" class="text-danger"></span>"?
        </p>
        <p class="text-gray-600">This action cannot be undone.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
        <form id="delete_role_form" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete Role</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Edit Role Modal
    document.querySelectorAll(".btn-edit-role").forEach(button => {
        button.addEventListener("click", function() {
            let roleId = this.dataset.id;
            let roleName = this.dataset.name;
            let permissions = JSON.parse(this.dataset.permissions);

            document.getElementById("edit_role_id").value = roleId;
            document.getElementById("edit_role_name").value = roleName;

            // Reset all checkboxes
            document.querySelectorAll(".edit-permission").forEach(chk => {
                chk.checked = permissions.includes(chk.value);
            });

            // Set form action
            document.getElementById("kt_modal_update_role_form").action = "/roles/" + roleId;
        });
    });

    // Delete Role Modal
    document.querySelectorAll(".btn-delete-role").forEach(button => {
        button.addEventListener("click", function() {
            let roleId = this.dataset.id;
            let roleName = this.dataset.name;

            // Set role name in confirmation text
            document.getElementById("delete_role_name").textContent = roleName;
            
            // Set form action
            document.getElementById("delete_role_form").action = "/roles/" + roleId;

            // Show modal
            new bootstrap.Modal(document.getElementById('kt_modal_delete_role')).show();
        });
    });

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush