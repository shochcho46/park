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
        <div class="container">
            <div class="row">
                <h1 class="mt-3">Role List</h1>
                <div class="d-flex justify-content-between align-items-end mt-3 mb-3">


                    <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-end gap-1">
                        {{-- @include('components.daterange') --}}
                        @include('components.search')
                    </form>

                    <a href="{{ route('admin.roleCreate') }}" class="btn btn-outline-primary">
                         <i class="mdi mdi-plus-circle mdi-12px"></i>
                    </a>
                </div>


                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $index => $item)
                            <tr>
                                <th scope="row">{{ $datas->firstItem() + $index }}</th>
                                <td>{{ $item->name }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.roleEdit', $item->id) }}" class="btn btn-sm btn-outline-info"> <i class="mdi mdi-pencil "></i></a>

                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.roleDestroy', $item->id) }}">
                                        <i class="mdi mdi-delete"></i>
                                    </button>

                                    <a href="{{ route('admin.roleWithPermission', $item->id) }}" class="btn btn-sm btn-outline-success"> <i class="mdi mdi-vector-square-plus"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No items found.</td>
                            </tr>
                        @endforelse
                        @include('components.delete')
                        </tbody>
                    </table>

                    <div class="d-flex mt-3">
                        {{ $datas->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection



@push('custome-js')

@endpush
