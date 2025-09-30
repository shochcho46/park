@extends('layouts.app')

@push('custome-css')

@endpush

@section('content')

<div class="app-content-header"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Account Management</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="{{ route('admin.account.index') }}">Account</a>
                    </li>
                </ol>
            </div>
        </div> <!--end::Row-->
    </div> <!--end::Container-->
</div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <h1 class="mt-3">Account List</h1>

                <!-- Filter and Export Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Filters & Export</h5>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" action="{{ url()->current() }}" method="GET" class="row align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.account.index') }}" class="btn btn-secondary">Clear</a>
                                    <button type="button" class="btn btn-success" onclick="exportData()">
                                        <i class="mdi mdi-microsoft-excel"></i> Export
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3">
                            <label class="form-label">Export Types:</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="export_income" value="1" checked>
                                    <label class="form-check-label" for="export_income">Income</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="export_expense" value="2" checked>
                                    <label class="form-check-label" for="export_expense">Expense</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-end mt-3 mb-3">
                    <a href="{{ route('admin.account.create') }}" class="btn btn-outline-primary">
                         <i class="mdi mdi-plus-circle mdi-12px"></i> Add Account
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Category</th>
                            <th scope="col">Number Ticket</th>
                            <th scope="col">Ticket Price</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Type</th>
                            <th scope="col">Document</th>
                            <th scope="col">Note</th>
                            <th scope="col">Created At</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($datas as $index => $item)
                            <tr>
                                <th scope="row">{{ $datas->firstItem() + $index }}</th>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>{{ $item->number_ticket ?? '-' }}</td>
                                <td>
                                    @if(isset($item->ticket_price))
                                        ৳{{ number_format($item->ticket_price, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><strong>৳{{ number_format($item->totalAmount, 2) }}</strong></td>
                                <td>
                                    <span class="badge {{ $item->type_badge_class }}">
                                        {{ $item->type_text }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->getFirstMediaUrl('account_docs'))
                                        <a href="{{ $item->getFirstMediaUrl('account_docs') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-file-document"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->note ?? '-', 30) }}</td>
                                <td>{{ $item->created_at->format('M d, Y') }}</td>

                                <td class="text-center">

                                    <a href="{{ route('admin.account.edit', $item->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.account.destroy', $item->id) }}" title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No accounts found.</td>
                            </tr>
                        @endforelse
                        @include('components.delete')
                        </tbody>
                    </table>

                    <div class="d-flex mt-3">
                        {{ $datas->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection





@push('custome-js')
<script>
$(document).ready(function() {
    // Ensure at least one export type is selected
    $('#export_income, #export_expense').on('change', function() {
        var incomeChecked = $('#export_income').is(':checked');
        var expenseChecked = $('#export_expense').is(':checked');

        if (!incomeChecked && !expenseChecked) {
            // If no boxes are checked, check both
            $('#export_income, #export_expense').prop('checked', true);

            // Show warning
            if (typeof toastr !== 'undefined') {
                toastr.warning('At least one type must be selected. Both types have been selected.');
            } else {
                alert('At least one type must be selected. Both types have been selected.');
            }
        }
    });
});

// Export function
function exportData() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Get selected export types
    const incomeChecked = document.getElementById('export_income').checked;
    const expenseChecked = document.getElementById('export_expense').checked;

    // Add type parameters
    if (incomeChecked) formData.append('type[]', '1');
    if (expenseChecked) formData.append('type[]', '2');

    // Create URL with parameters
    const params = new URLSearchParams(formData);
    const exportUrl = '{{ route("admin.account.export") }}?' + params.toString();

    // Open in new window
    window.open(exportUrl, '_blank');
}
</script>
@endpush
