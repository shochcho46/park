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

        $startDate = Carbon::parse($request->start_date)->startOfDay() ?? null;
        $endDate = Carbon::parse($request->end_date)->endOfDay() ?? null;

        $datas = Account::with('category')
                    ->orderBy('id', 'desc')
                    ->when($request->search, function($query) use ($request){
                        $query->where('note', 'like', '%'.$request->search.'%')
                              ->orWhereHas('category', function($q) use ($request) {
                                  $q->where('name', 'like', '%'.$request->search.'%');
                              });
                    })
                    ->dateRange($startDate, $endDate)
                    ->paginate($limit);

        return view('admin::account.index', compact('datas'));
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
            'type.*' => 'in:1,2'
        ]);

        // Generate filename with current date and filters
        $filename = 'accounts_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new AccountsExport($request), $filename);
    }
}
