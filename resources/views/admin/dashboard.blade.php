@extends('layouts.app')

@push('custome-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
<style>
    #monthlyChart {
        max-height: 400px;
    }
    .table-responsive {
        max-height: 700px;
        overflow-y: auto;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    #categoryMonthlyTable td {
        white-space: nowrap;
        padding: 0.4rem 0.3rem;
    }
    #categoryMonthlyTable th {
        padding: 0.5rem 0.3rem;
    }
    .profit-positive {
        color: #28a745;
        font-weight: 600;
    }
    .profit-negative {
        color: #dc3545;
        font-weight: 600;
    }
    .profit-zero {
        color: #6c757d;
    }
    #categoryMonthlyTable tbody tr:nth-of-type(odd) {
        background-color: #f8f9fa;
    }
    #categoryMonthlyTable tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
</style>
@endpush

@section('content')

    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

     <!--begin::App Content-->

     <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 1-->
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h6>{{ $data['categories'] }}</h6>
                            <p>Total Categories</p>
                        </div> <span class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <i class="mdi mdi-shape-outline"></i>
                        </span> <a href="{{ route('admin.category.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i> </a>
                    </div> <!--end::Small Box Widget 1-->
                </div> <!--end::Col-->
                <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 2-->
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h6>&#2547; {{ $data['income'] }}</h6>
                            <p>Total Income</p>
                        </div> <span class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <i class="mdi mdi-finance"></i>
                        </span> <a href="{{ route('admin.account.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i> </a>
                    </div> <!--end::Small Box Widget 2-->
                </div> <!--end::Col-->
                <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 3-->
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h6>&#2547; {{ $data['expenses'] }}</h6>
                            <p>Total Expenses</p>
                        </div> <span class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <i class="mdi mdi-trending-down"></i>
                        </span> <a href="{{ route('admin.account.index') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i> </a>
                    </div> <!--end::Small Box Widget 3-->
                </div> <!--end::Col-->
                <div class="col-lg-3 col-6"> <!--begin::Small Box Widget 4-->
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h6>&#2547; {{ $data['revenue'] }}</h6>
                            <p>Total Revenue</p>
                        </div> <span class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <i class="mdi mdi-currency-usd"></i>
                        </span> <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i> </a>
                    </div> <!--end::Small Box Widget 4-->
                </div> <!--end::Col-->
            </div> <!--end::Row--> <!--begin::Row-->

            <!-- Monthly Chart Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Overall Monthly Income, Expense & Profit</h5>
                            <div class="card-tools d-flex gap-2">
                                <select id="categoryFilter" class="form-select form-select-sm" style="width: auto;">
                                    <option value="all">All Categories</option>
                                </select>
                                <select id="yearFilter" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartLoader" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading chart data...</p>
                            </div>
                            <canvas id="monthlyChart" style="display: none; height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category-wise Table Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Category-wise Monthly Expense, Income & Profit/Loss</h5>
                            <div class="card-tools d-flex gap-2">
                                <span class="badge bg-primary">Filter:</span>
                                <select id="categoryFilter2" class="form-select form-select-sm" style="width: auto;">
                                    <option value="all">All Categories</option>
                                </select>
                                <select id="yearFilter2" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Loading...</option>
                                </select>
                                <button class="btn btn-sm btn-info" onclick="syncFilters()">
                                    <i class="bi bi-arrow-repeat"></i> Sync
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="dataLoader" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading data...</p>
                            </div>
                            <div id="categoryTableContainer" style="display: none;">
                                <!-- Overall summary will be here -->
                                <div id="overallSummary" class="alert alert-info mb-3"></div>

                                <!-- Table will be here -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm" id="categoryMonthlyTable">
                                        <thead class="table-dark sticky-top">
                                            <tr>
                                                <th class="text-center align-middle" rowspan="2" style="min-width: 150px;">Category</th>
                                                <th class="text-center" colspan="2">Jan</th>
                                                <th class="text-center" colspan="2">Feb</th>
                                                <th class="text-center" colspan="2">Mar</th>
                                                <th class="text-center" colspan="2">Apr</th>
                                                <th class="text-center" colspan="2">May</th>
                                                <th class="text-center" colspan="2">Jun</th>
                                                <th class="text-center" colspan="2">Jul</th>
                                                <th class="text-center" colspan="2">Aug</th>
                                                <th class="text-center" colspan="2">Sep</th>
                                                <th class="text-center" colspan="2">Oct</th>
                                                <th class="text-center" colspan="2">Nov</th>
                                                <th class="text-center" colspan="2">Dec</th>
                                                <th class="text-center align-middle bg-secondary" rowspan="2" style="min-width: 100px;">Total</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                                <th class="text-center" style="min-width: 70px;">Exp</th>
                                                <th class="text-center" style="min-width: 70px;">Inc</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoryTableBody">
                                            <!-- Data will be populated via AJAX -->
                                        </tbody>
                                        <tfoot class="table-secondary fw-bold">
                                            <tr id="totalExpenseRow">
                                                <!-- Total Expense row -->
                                            </tr>
                                            <tr id="totalIncomeRow">
                                                <!-- Total Income row -->
                                            </tr>
                                            <tr id="totalProfitRow">
                                                <!-- Total Profit row -->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!--end::Container-->
    </div>
     <!--end::App Content-->
@endsection

@push('custome-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let monthlyChart = null;

    // Load available years and categories on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAvailableYears();
        loadCategories();

        // Add event listeners for filters (both sets)
        document.getElementById('yearFilter').addEventListener('change', function() {
            document.getElementById('yearFilter2').value = this.value;
            if (this.value) {
                loadMonthlyData();
            }
        });

        document.getElementById('yearFilter2').addEventListener('change', function() {
            document.getElementById('yearFilter').value = this.value;
            if (this.value) {
                loadMonthlyData();
            }
        });

        document.getElementById('categoryFilter').addEventListener('change', function() {
            document.getElementById('categoryFilter2').value = this.value;
            loadMonthlyData();
        });

        document.getElementById('categoryFilter2').addEventListener('change', function() {
            document.getElementById('categoryFilter').value = this.value;
            loadMonthlyData();
        });
    });

    function syncFilters() {
        // Ensure both filter sets are in sync
        document.getElementById('yearFilter2').value = document.getElementById('yearFilter').value;
        document.getElementById('categoryFilter2').value = document.getElementById('categoryFilter').value;
        loadMonthlyData();
    }

    function loadCategories() {
        fetch('{{ route("admin.getCategories") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoryFilter = document.getElementById('categoryFilter');
                    const categoryFilter2 = document.getElementById('categoryFilter2');

                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categoryFilter.appendChild(option);

                        const option2 = document.createElement('option');
                        option2.value = category.id;
                        option2.textContent = category.name;
                        categoryFilter2.appendChild(option2);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading categories:', error);
            });
    }

    function loadAvailableYears() {
        fetch('{{ route("admin.getAvailableYears") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const yearFilter = document.getElementById('yearFilter');
                    const yearFilter2 = document.getElementById('yearFilter2');
                    yearFilter.innerHTML = '';
                    yearFilter2.innerHTML = '';

                    if (data.years.length > 0) {
                        data.years.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = 'FY ' + year;
                            yearFilter.appendChild(option);

                            const option2 = document.createElement('option');
                            option2.value = year;
                            option2.textContent = 'FY ' + year;
                            yearFilter2.appendChild(option2);
                        });

                        // Set current fiscal year as default
                        const today = new Date();
                        const currentYear = today.getFullYear();
                        const currentMonth = today.getMonth() + 1; // JavaScript months are 0-indexed

                        // If current month is July (7) or later, fiscal year is current-next
                        // If current month is before July, fiscal year is previous-current
                        let currentFiscalYear;
                        if (currentMonth >= 7) {
                            currentFiscalYear = currentYear + '-' + (currentYear + 1);
                        } else {
                            currentFiscalYear = (currentYear - 1) + '-' + currentYear;
                        }

                        if (data.years.includes(currentFiscalYear)) {
                            yearFilter.value = currentFiscalYear;
                            yearFilter2.value = currentFiscalYear;
                        } else {
                            yearFilter.value = data.years[0];
                            yearFilter2.value = data.years[0];
                        }

                        // Load data for the selected year
                        loadMonthlyData();
                    } else {
                        yearFilter.innerHTML = '<option value="">No data available</option>';
                        yearFilter2.innerHTML = '<option value="">No data available</option>';
                        document.getElementById('chartLoader').innerHTML = '<p class="text-muted">No data available</p>';
                        document.getElementById('dataLoader').innerHTML = '<p class="text-muted">No data available</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading years:', error);
                document.getElementById('yearFilter').innerHTML = '<option value="">Error loading years</option>';
                document.getElementById('yearFilter2').innerHTML = '<option value="">Error loading years</option>';
            });
    }

    function loadMonthlyData() {
        const year = document.getElementById('yearFilter').value;
        const categoryId = document.getElementById('categoryFilter').value;

        if (!year) return;

        // Show loaders
        document.getElementById('chartLoader').style.display = 'block';
        document.getElementById('monthlyChart').style.display = 'none';
        document.getElementById('dataLoader').style.display = 'block';
        document.getElementById('categoryTableContainer').style.display = 'none';

        const url = `{{ route("admin.getMonthlyData") }}?year=${year}&category_id=${categoryId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide loaders
                    document.getElementById('chartLoader').style.display = 'none';
                    document.getElementById('monthlyChart').style.display = 'block';
                    document.getElementById('dataLoader').style.display = 'none';
                    document.getElementById('categoryTableContainer').style.display = 'block';

                    // Render chart with overall totals
                    renderChart(data.months, data.totalsByMonth);

                    // Populate table with category data
                    populateCategoryTable(data.categoryWiseData, data.months, data.overallTotals);
                }
            })
            .catch(error => {
                console.error('Error loading monthly data:', error);
                document.getElementById('chartLoader').innerHTML = '<p class="text-danger">Error loading chart data</p>';
                document.getElementById('dataLoader').innerHTML = '<p class="text-danger">Error loading data</p>';
            });
    }

    function renderChart(months, totalsByMonth) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');

        // Destroy existing chart if it exists
        if (monthlyChart) {
            monthlyChart.destroy();
        }

        monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        data: totalsByMonth.income,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        type: 'bar',
                        order: 2
                    },
                    {
                        label: 'Expense',
                        data: totalsByMonth.expense,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        type: 'bar',
                        order: 3
                    },
                    {
                        label: 'Profit/Loss',
                        data: totalsByMonth.profit,
                        backgroundColor: 'transparent',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 3,
                        type: 'line',
                        fill: false,
                        tension: 0.4,
                        order: 1,
                        pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '৳ ' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    function populateCategoryTable(categoryWiseData, months, overallTotals) {
        // Update overall summary
        const summaryDiv = document.getElementById('overallSummary');
        summaryDiv.innerHTML = `
            <div class="row text-center">
                <div class="col-md-4">
                    <h6 class="mb-0">Total Income</h6>
                    <h4 class="text-success mb-0">৳ ${overallTotals.income.toLocaleString()}</h4>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-0">Total Expense</h6>
                    <h4 class="text-warning mb-0">৳ ${overallTotals.expense.toLocaleString()}</h4>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-0">Total Profit</h6>
                    <h4 class="${overallTotals.profit >= 0 ? 'text-success' : 'text-danger'} mb-0">৳ ${overallTotals.profit.toLocaleString()}</h4>
                </div>
            </div>
        `;

        // Populate table body
        const tbody = document.getElementById('categoryTableBody');
        tbody.innerHTML = '';

        let monthlyExpenseTotals = Array(12).fill(0);
        let monthlyIncomeTotals = Array(12).fill(0);
        let monthlyProfitTotals = Array(12).fill(0);

        categoryWiseData.forEach(category => {
            // Category data row (Expense and Income)
            const categoryRow = document.createElement('tr');
            categoryRow.className = 'table-light';

            let categoryRowHTML = `<td class="fw-bold">${category.category_name}</td>`;

            // Add expense and income for each month
            category.months.forEach((monthData, index) => {
                const expense = monthData.expense;
                const income = monthData.income;

                monthlyExpenseTotals[index] += expense;
                monthlyIncomeTotals[index] += income;
                monthlyProfitTotals[index] += (income - expense);

                categoryRowHTML += `
                    <td class="text-end text-warning" style="font-size: 0.85rem;">৳${expense.toLocaleString()}</td>
                    <td class="text-end text-success" style="font-size: 0.85rem;">৳${income.toLocaleString()}</td>
                `;
            });

            // Add total column
            categoryRowHTML += `<td class="text-end fw-bold bg-light">৳ ${category.total_profit.toLocaleString()}</td>`;

            categoryRow.innerHTML = categoryRowHTML;
            tbody.appendChild(categoryRow);

            // Profit/Loss row for this category
            const profitRow = document.createElement('tr');
            profitRow.style.borderBottom = '2px solid #dee2e6';

            let profitRowHTML = `<td class="fst-italic ps-4" style="font-size: 0.9rem;">↳ Profit/Loss</td>`;

            // Add profit/loss for each month
            category.months.forEach(monthData => {
                const profit = monthData.profit;
                let profitClass = 'profit-zero';
                if (profit > 0) profitClass = 'profit-positive';
                else if (profit < 0) profitClass = 'profit-negative';

                profitRowHTML += `<td colspan="2" class="text-center fw-bold ${profitClass}" style="font-size: 0.85rem;">৳ ${profit.toLocaleString()}</td>`;
            });

            // Add total profit
            const totalProfit = category.total_profit;
            let totalClass = 'profit-zero';
            if (totalProfit > 0) totalClass = 'profit-positive';
            else if (totalProfit < 0) totalClass = 'profit-negative';

            profitRowHTML += `<td class="text-end fw-bold ${totalClass}">৳ ${totalProfit.toLocaleString()}</td>`;

            profitRow.innerHTML = profitRowHTML;
            tbody.appendChild(profitRow);
        });

        // Populate footer totals
        // Total Expense Row
        const totalExpenseRow = document.getElementById('totalExpenseRow');
        let expenseHTML = '<td class="text-center">TOTAL EXPENSE</td>';

        monthlyExpenseTotals.forEach(expense => {
            expenseHTML += `<td colspan="2" class="text-center text-warning">৳ ${expense.toLocaleString()}</td>`;
        });
        expenseHTML += `<td class="text-end text-warning">৳ ${overallTotals.expense.toLocaleString()}</td>`;
        totalExpenseRow.innerHTML = expenseHTML;

        // Total Income Row
        const totalIncomeRow = document.getElementById('totalIncomeRow');
        let incomeHTML = '<td class="text-center">TOTAL INCOME</td>';

        monthlyIncomeTotals.forEach(income => {
            incomeHTML += `<td colspan="2" class="text-center text-success">৳ ${income.toLocaleString()}</td>`;
        });
        incomeHTML += `<td class="text-end text-success">৳ ${overallTotals.income.toLocaleString()}</td>`;
        totalIncomeRow.innerHTML = incomeHTML;

        // Total Profit Row
        const totalProfitRow = document.getElementById('totalProfitRow');
        let profitHTML = '<td class="text-center">TOTAL PROFIT/LOSS</td>';

        monthlyProfitTotals.forEach(profit => {
            let profitClass = 'profit-zero';
            if (profit > 0) profitClass = 'profit-positive';
            else if (profit < 0) profitClass = 'profit-negative';

            profitHTML += `<td colspan="2" class="text-center fw-bold ${profitClass}">৳ ${profit.toLocaleString()}</td>`;
        });

        const grandTotal = overallTotals.profit;
        let grandTotalClass = 'profit-zero';
        if (grandTotal > 0) grandTotalClass = 'profit-positive';
        else if (grandTotal < 0) grandTotalClass = 'profit-negative';

        profitHTML += `<td class="text-end fw-bold ${grandTotalClass}">৳ ${grandTotal.toLocaleString()}</td>`;
        totalProfitRow.innerHTML = profitHTML;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script>
<script>
    const connectedSortables =
        document.querySelectorAll(".connectedSortable");
    connectedSortables.forEach((connectedSortable) => {
        let sortable = new Sortable(connectedSortable, {
            group: "shared",
            handle: ".card-header",
        });
    });

    const cardHeaders = document.querySelectorAll(
        ".connectedSortable .card-header",
    );
    cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = "move";
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js" integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>
<script>
    // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
    // IT'S ALL JUST JUNK FOR DEMO
    // ++++++++++++++++++++++++++++++++++++++++++

    const sales_chart_options = {
        series: [{
                name: "Digital Goods",
                data: [28, 48, 40, 19, 86, 27, 90],
            },
            {
                name: "Electronics",
                data: [65, 59, 80, 81, 56, 55, 40],
            },
        ],
        chart: {
            height: 300,
            type: "area",
            toolbar: {
                show: false,
            },
        },
        legend: {
            show: false,
        },
        colors: ["#0d6efd", "#20c997"],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
        },
        xaxis: {
            type: "datetime",
            categories: [
                "2023-01-01",
                "2023-02-01",
                "2023-03-01",
                "2023-04-01",
                "2023-05-01",
                "2023-06-01",
                "2023-07-01",
            ],
        },
        tooltip: {
            x: {
                format: "MMMM yyyy",
            },
        },
    };

    const sales_chart = new ApexCharts(
        document.querySelector("#revenue-chart"),
        sales_chart_options,
    );
    sales_chart.render();
</script>

<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js" integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js" integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script> <!-- jsvectormap -->
    <script>
        const visitorsData = {
            US: 398, // USA
            SA: 400, // Saudi Arabia
            CA: 1000, // Canada
            DE: 500, // Germany
            FR: 760, // France
            CN: 300, // China
            AU: 700, // Australia
            BR: 600, // Brazil
            IN: 800, // India
            GB: 320, // Great Britain
            RU: 3000, // Russia
        };

        // World map by jsVectorMap
        const map = new jsVectorMap({
            selector: "#world-map",
            map: "world",
        });

        // Sparkline charts
        const option_sparkline1 = {
            series: [{
                data: [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021],
            }, ],
            chart: {
                type: "area",
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: "straight",
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ["#DCE6EC"],
        };

        const sparkline1 = new ApexCharts(
            document.querySelector("#sparkline-1"),
            option_sparkline1,
        );
        sparkline1.render();

        const option_sparkline2 = {
            series: [{
                data: [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921],
            }, ],
            chart: {
                type: "area",
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: "straight",
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ["#DCE6EC"],
        };

        const sparkline2 = new ApexCharts(
            document.querySelector("#sparkline-2"),
            option_sparkline2,
        );
        sparkline2.render();

        const option_sparkline3 = {
            series: [{
                data: [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21],
            }, ],
            chart: {
                type: "area",
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: "straight",
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ["#DCE6EC"],
        };

        const sparkline3 = new ApexCharts(
            document.querySelector("#sparkline-3"),
            option_sparkline3,
        );
        sparkline3.render();
    </script>

@endpush
