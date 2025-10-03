<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Admin\Entities\Account;
use Modules\Admin\Entities\Category;
use Modules\Admin\Exports\AccountsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 20;

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $query = Account::with('category')
                    ->orderBy('id', 'desc')
                    ->when($request->search, function($query) use ($request){
                        $query->where('note', 'like', '%'.$request->search.'%')
                              ->orWhereHas('category', function($q) use ($request) {
                                  $q->where('name', 'like', '%'.$request->search.'%');
                              });
                    })
                    ->when($request->category_id, function($query) use ($request) {
                        $query->where('category_id', $request->category_id);
                    });

        if ($startDate || $endDate) {
            $query->whereBetween('created_at', [
                $startDate ?? Carbon::minValue(),
                $endDate ?? Carbon::maxValue()
            ]);
        }

        $datas = $query->paginate($limit);

        // Get categories for dropdown
        $categories = Category::active()->orderBy('name')->get();

        // Calculate totals for summary
        $totalQuery = Account::query();
        
        // Apply same filters for totals
        if ($request->search) {
            $totalQuery->where(function($q) use ($request) {
                $q->where('note', 'like', '%'.$request->search.'%')
                  ->orWhereHas('category', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%'.$request->search.'%');
                  });
            });
        }
        
        if ($request->category_id) {
            $totalQuery->where('category_id', $request->category_id);
        }
        
        if ($startDate || $endDate) {
            $totalQuery->whereBetween('created_at', [
                $startDate ?? Carbon::minValue(),
                $endDate ?? Carbon::maxValue()
            ]);
        }

        $totalIncome = $totalQuery->clone()->where('type', 1)->sum('totalAmount');
        $totalExpense = $totalQuery->clone()->where('type', 2)->sum('totalAmount');

        return view('admin::account.index', compact('datas', 'categories', 'totalIncome', 'totalExpense'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin::account.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'number_ticket' => 'nullable|numeric|min:0',
            'ticket_price' => 'nullable|numeric|min:0',
            'totalAmount' => 'required|numeric|min:0',
            'type' => 'required|in:1,2',
            'note' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:jpeg,png,gif,pdf,doc,docx|max:10240', // 10MB max
        ]);

        $account = Account::create($validatedData);

        // Handle file upload
        if ($request->hasFile('document')) {
            $account->addMediaFromRequest('document')
                ->toMediaCollection('account_docs');
        }

        $toaster = [
            'message' => 'Account created successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.account.index')->with($toaster);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $data = $account;
        return view('admin::account.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $categories = Category::active()->orderBy('name')->get();
        $data = $account;
        return view('admin::account.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account): RedirectResponse
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'number_ticket' => 'nullable|numeric|min:0',
            'ticket_price' => 'nullable|numeric|min:0',
            'totalAmount' => 'required|numeric|min:0',
            'type' => 'required|in:1,2',
            'note' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:jpeg,png,gif,pdf,doc,docx|max:10240', // 10MB max
        ]);

        $account->update($validatedData);

        // Handle file upload
        if ($request->hasFile('document')) {
            // Clear existing document first
            $account->clearMediaCollection('account_docs');

            $account->addMediaFromRequest('document')
                ->toMediaCollection('account_docs');
        }

        $toaster = [
            'message' => 'Account updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.account.index')->with($toaster);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        $toaster = [
            'message' => 'Account deleted successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.account.index')->with($toaster);
    }

    /**
     * Export accounts to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'nullable|array',
            'type.*' => 'in:1,2',
            'category_id' => 'nullable|exists:categories,id',
            'search' => 'nullable|string'
        ]);

        // Generate filename with current date and filters
        $filename = 'accounts_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new AccountsExport($request), $filename);
    }
}
