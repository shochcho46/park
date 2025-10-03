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
                <h1 class="mt-3">Create Account</h1>
                <div class="text-end m-3">
                    <a href="{{ route('admin.account.index') }}" class="btn btn-outline-primary"><span class="mdi mdi-format-list-text"></span> Back to List</a>
                </div>

                <div class="card card-primary card-outline mb-4"> <!--begin::Header-->
                    <div class="card-header">
                        <div class="card-title">Create New Account</div>
                    </div> <!--end::Header--> <!--begin::Form-->
                    <form action="{{route('admin.account.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Type <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="type" id="type_income" value="1" {{ old('type', '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_income">
                                                    Income
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="type" id="type_expense" value="2" {{ old('type') == '2' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="type_expense">
                                                    Expense/Maintenance
                                                </label>
                                            </div>
                                        </div>
                                        @error('type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="ticket_fields">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="number_ticket" class="form-label">Number of Tickets</label>
                                        <input type="number" class="form-control" id="number_ticket" name="number_ticket" placeholder="0" value="{{ old('number_ticket') }}" min="0" step="1">
                                        @error('number_ticket')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ticket_price" class="form-label">Ticket Price</label>
                                        <input type="number" class="form-control" id="ticket_price" name="ticket_price" placeholder="0.00" value="{{ old('ticket_price') }}" min="0" step="0.01">
                                        @error('ticket_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="totalAmount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="totalAmount" name="totalAmount" placeholder="0.00" value="{{ old('totalAmount', 0) }}" step="0.01" required>
                                        <small class="form-text text-muted" id="total_help">Auto-calculated: Number of Tickets × Ticket Price</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="document" class="form-label">Upload Document</label>
                                <input type="file" class="form-control" id="document" name="document" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX (Max: 10MB)</small>
                                @error('document')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter additional notes...">{{ old('note') }}</textarea>
                                @error('note')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="custom_datetime" class="form-label">Date & Time</label>
                                <input type="datetime-local" class="form-control" id="custom_datetime" name="custom_datetime" value="{{ old('custom_datetime', now()->format('Y-m-d\TH:i')) }}">
                                <small class="form-text text-muted">Set the date and time for this account entry. This will be used for both created_at and updated_at timestamps.</small>
                                @error('custom_datetime')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div> <!--end::Body--> <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.account.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div> <!--end::Footer-->
                    </form> <!--end::Form-->
                </div>

            </div>
        </div>
    </div>
@endsection



@push('custome-js')
<script>
$(document).ready(function() {
    // Handle type change to show/hide ticket fields
    function handleTypeChange() {
        var selectedType = $('input[name="type"]:checked').val();

        if (selectedType == '2') { // Expense
            $('#ticket_fields').hide();
            $('#totalAmount').prop('readonly', false);
            $('#total_help').text('Enter the total expense amount');
            // Clear ticket fields when hiding
            $('#number_ticket, #ticket_price').val('');
        } else { // Income
            $('#ticket_fields').show();
            $('#total_help').text('Auto-calculated: Number of Tickets × Ticket Price or enter manually');
            calculateTotal(); // Recalculate if values exist
        }
    }

    // Auto-calculate total amount only when both ticket fields have values
    function calculateTotal() {
        var numberTicket = parseFloat($('#number_ticket').val()) || 0;
        var ticketPrice = parseFloat($('#ticket_price').val()) || 0;

        // Only auto-calculate if both fields have values
        if (numberTicket > 0 && ticketPrice > 0) {
            var total = numberTicket * ticketPrice;
            $('#totalAmount').val(total.toFixed(2));
            $('#totalAmount').prop('readonly', true);
        } else {
            // If either field is empty, allow manual input
            $('#totalAmount').prop('readonly', false);
        }
    }

    // Bind events
    $('input[name="type"]').on('change', handleTypeChange);
    $('#number_ticket, #ticket_price').on('keyup change', function() {
        if ($('input[name="type"]:checked').val() == '1') { // Only for income
            calculateTotal();
        }
    });

    // Initialize on page load
    handleTypeChange();
});
</script>
@endpush
