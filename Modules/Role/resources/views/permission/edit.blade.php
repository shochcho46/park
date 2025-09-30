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
                        <a href="{{ route('admin.permissionIndex') }}">Permission</a>
                    </li>
                </ol>
            </div>
        </div> <!--end::Row-->
    </div> <!--end::Container-->
</div>

    <div class="app-content">
        <div class="container">
            <div class="row">
                <h1 class="mt-3">Permission List</h1>
                <div class="text-end m-3">
                    <a href="{{ route('admin.permissionIndex') }}" class="btn btn-outline-primary"><span class="mdi mdi-format-list-text"></span></a>
                </div>

                <div class="card card-primary card-outline mb-4"> <!--begin::Header-->
                    <div class="card-header">
                        <div class="card-title">Edit Permission</div>
                    </div> <!--end::Header--> <!--begin::Form-->
                    <form action="{{route('admin.permissionUpdate',$data->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Permission Namm</label>
                                <input type="text" class="form-control text-lowercase" id="name" name="name" placeholder="Permission Name" value="{{ $data->name }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div> <!--end::Body--> <!--begin::Footer-->
                        <div class="card-footer"> <button type="submit" class="btn btn-primary">Update</button> </div> <!--end::Footer-->
                    </form> <!--end::Form-->
                </div>

            </div>
        </div>
    </div>
@endsection



@push('custome-js')

@endpush
