@extends('admin::layouts.app')

@section('title', 'Account Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-3">Account Details</h1>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Account Information</h5>
                <div>
                    <a href="{{ route('admin.account.edit', $data->id) }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('admin.account.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Category:</strong>
                        <span class="text-muted">{{ $data->category->name ?? 'N/A' }}</span>
                    </div>

                    @if($data->type == 1)
                        <div class="mb-3">
                            <strong>Number of Tickets:</strong>
                            <span class="text-muted">{{ $data->number_ticket ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Ticket Price:</strong>
                            <span class="text-muted">৳{{ number_format($data->ticket_price ?? 0, 2) }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Total Amount:</strong>
                        <span class="text-muted">৳{{ number_format($data->totalAmount, 2) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Type:</strong>
                        <span class="badge {{ $data->type == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $data->type == 1 ? 'Income' : 'Expense' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Created Date:</strong>
                        <span class="text-muted">{{ $data->created_at->format('M d, Y h:i A') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Last Updated:</strong>
                        <span class="text-muted">{{ $data->updated_at->format('M d, Y h:i A') }}</span>
                    </div>

                    @if($data->getFirstMedia('account_docs'))
                        <div class="mb-3">
                            <strong>Document:</strong>
                            <div class="mt-2">
                                @php
                                    $media = $data->getFirstMedia('account_docs');
                                    $isImage = in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/gif']);
                                @endphp

                                @if($isImage)
                                    <img src="{{ $media->getUrl() }}" alt="Document" class="img-thumbnail" style="max-width: 200px;">
                                @else
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-file-document"></i> View Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($data->note)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>Note:</strong>
                                <p class="mb-0 mt-2">{{ $data->note }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
