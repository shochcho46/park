<div class="d-flex flex-column">
    <label for="from_date" class="form-label mb-1">From:</label>
    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
</div>

<div class="d-flex flex-column">
    <label for="to_date" class="form-label mb-1">To:</label>
    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
</div>
