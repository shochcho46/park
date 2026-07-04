@extends('layouts.app')

@push('custome-css')
<style>
.dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
}
.dropdown-menu li:hover {
    background-color: #f8f9fa;
}
.form-check-input:checked + .form-check-label {
    font-weight: 600;
    color: #0d6efd;
}
#categoryButtonText {
    display: inline-block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 90%;
}
</style>
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
                        <form id="filterForm" action="{{ url()->current() }}" method="GET">
                            <!-- Row 1: Date Filters -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                </div>
                            </div>

                            <!-- Row 2: Category -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="category_id" class="form-label">Category</label>
                                    <div class="dropdown w-100">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="categoryButtonText">Select categories...</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2" aria-labelledby="categoryDropdown" style="max-height: 300px; overflow-y: auto;" onclick="event.stopPropagation()">
                                            <li class="mb-2">
                                                <button type="button" class="btn btn-sm btn-primary me-2" onclick="selectAllCategories()">Select All</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="clearAllCategories()">Clear All</button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @foreach($categories as $category)
                                                <li class="px-2 py-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input category-checkbox" type="checkbox" name="category_id[]" value="{{ $category->id }}" id="category_{{ $category->id }}" {{ in_array($category->id, (array)request('category_id', [])) ? 'checked' : '' }} onchange="updateCategoryButtonText()">
                                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Type Checkboxes -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Type</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="type_income" name="type[]" value="1" {{ in_array('1', (array)request('type', ['1', '2'])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_income">Income</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="type_expense" name="type[]" value="2" {{ in_array('2', (array)request('type', ['1', '2'])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_expense">Expense</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4: Search -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Row 5: Action Buttons -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.account.index') }}" class="btn btn-secondary">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-success" onclick="exportData()">
                                <i class="mdi mdi-microsoft-excel"></i> Export Filtered Results
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Income</h6>
                                <h3 class="mb-0">৳{{ number_format($totalIncome, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Expense</h6>
                                <h3 class="mb-0">৳{{ number_format($totalExpense, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card {{ ($totalIncome - $totalExpense) >= 0 ? 'bg-info' : 'bg-warning' }} text-white">
                            <div class="card-body">
                                <h6 class="card-title">Net Balance</h6>
                                <h3 class="mb-0">৳{{ number_format($totalIncome - $totalExpense, 2) }}</h3>
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
    // Initialize category button text on page load
    updateCategoryButtonText();

    // Ensure at least one type is selected for filtering
    $('#type_income, #type_expense').on('change', function() {
        var incomeChecked = $('#type_income').is(':checked');
        var expenseChecked = $('#type_expense').is(':checked');

        if (!incomeChecked && !expenseChecked) {
            $(this).prop('checked', true);
            if (typeof toastr !== 'undefined') {
                toastr.warning('At least one type must be selected for filtering.');
            } else {
                alert('At least one type must be selected for filtering.');
            }
        }
    });
});

// Update category button text based on selected checkboxes
function updateCategoryButtonText() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    const buttonText = document.getElementById('categoryButtonText');

    if (checkboxes.length === 0) {
        buttonText.textContent = 'Select categories...';
        buttonText.style.color = '#6c757d';
    } else if (checkboxes.length === 1) {
        buttonText.textContent = checkboxes[0].nextElementSibling.textContent.trim();
        buttonText.style.color = '#212529';
    } else {
        buttonText.textContent = `Categories (${checkboxes.length} selected)`;
        buttonText.style.color = '#212529';
    }
}

// Select all categories
function selectAllCategories() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateCategoryButtonText();
}

// Clear all categories
function clearAllCategories() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateCategoryButtonText();
}

// Export function - exports the currently filtered results
function exportData() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Create URL with all current filter parameters
    const params = new URLSearchParams();

    // Add all form data to params (includes type[], category_id[], dates, search)
    // This correctly handles multiple values for the same key
    for (let [key, value] of formData.entries()) {
        params.append(key, value);
    }

    const exportUrl = '{{ route("admin.account.export") }}?' + params.toString();

    // Open in new window to download
    window.location.href = exportUrl;
}
</script>
@endpush
