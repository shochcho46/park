@extends('layouts.app')

@push('custome-css')

@endpush

@section('content')

<div class="app-content-header"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Role & Permision</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ route('admin.roleIndex') }}">Role</a>
                    </li>
                </ol>
            </div>
        </div> <!--end::Row-->
    </div> <!--end::Container-->
</div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <h1 class="mt-3">Assign Permission to Role: <strong>{{ $role->name }}</strong></h1>

                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <div class="card-title">Assign Permissions</div>
                    </div>
                    <form action="{{ route('admin.roleWithPermissionStore', $role->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                @foreach($permissions as $index => $permission)
                                    <div class="col-md-2 col-6">
                                        <div class="form-check m-2">
                                            <input type="checkbox" class="form-check-input" name="permission_list[]" value="{{ $permission->id }}"
                                                id="perm_{{ $permission->id }}"
                                                {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection



@push('custome-js')

@endpush
