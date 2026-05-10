<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPassMail;
use App\Models\Admin;
use App\Models\Country;
use App\Models\Gender;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator ;
use Modules\Admin\Entities\Account;
use Modules\Admin\Entities\Category;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;

class AdminController extends Controller
{

    public function adminLogin()
    {
        $datas = Country::all();
        $genders = Gender::all();
        return view('auth.admin.login',compact('datas','genders'));

    }

    public function loadForgetMyPass()
    {
        $datas = Country::all();
        return view('auth.admin.forget',compact('datas'));

    }

    public function findUser(Request $request)
    {
        $countryIso = Country::where('id',18)->first();

        $validated = $request->validate([
            'email_or_phone' => ['bail','required'],
            ],
            [
                'email_or_phone.regex' => 'The phone number must contain only English digits (0-9).',
                'email_or_phone.required' => 'The phone number is required',
            ]
        );

        if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {
            $credential = array("email" => $request->email_or_phone);
        }
        else
        {
            $phoneNumber = validationMobileNumber($request->email_or_phone,$countryIso->iso);
            $credential = array("phone" => $phoneNumber);
            $email = false;
        }

        $admin = Admin::where($credential)->first();

        if ($admin) {
            $toster = array(
                'message' => 'User Found',
                'alert-type' => 'success'
            );

            return redirect()->route('otpLoad')->with('uuid', $admin->id)->with($toster);

        }
        else
        {
            $toster = array(
                'message' => 'User Not Found',
                'alert-type' => 'error'
            );

            return back()->with( $toster);
        }
    }



    public function adminValidateLogin(Request $request)
    {

        $countryIso = Country::where('id',18)->first();

        $validated = $request->validate([
            'email_or_phone' => ['bail','required'],
            'password' => 'required',
            ],
            [
                'email_or_phone.regex' => 'The phone number must contain only English digits (0-9).',
                'email_or_phone.required' => 'The phone number is required',
            ]
        );

        if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {

            $credential = array("email" => $request->email_or_phone, "password" => $request->password);
        }
        else
        {
            $phoneNumber = validationMobileNumber($request->email_or_phone,$countryIso->iso);
            $credential = array("phone" => $phoneNumber, "password" => $request->password);
        }

        if (Auth::guard('admin')->attempt($credential)) {

            $user = Auth::guard('admin')->user();

            if (($user->status == 0)) {

                $toster = array(
                    'message' => 'This account is in black listed',
                    'alert-type' => 'error'
                );

                return back()->with( $toster);
            } else {

                return redirect()->route('admin.dashboard');
            }

        }


        else
        {
            $toster = array(
                'message' => 'Wrong Credential',
                'alert-type' => 'error'
            );

            return back()->with( $toster);

        }
    }
    public function dashboard()
    {
        $result = Account::selectRaw('
                        SUM(CASE WHEN type = 1 THEN totalAmount ELSE 0 END) as income,
                        SUM(CASE WHEN type = 2 THEN totalAmount ELSE 0 END) as expenses
                    ')->first();

        $data['income'] = $result->income ?? 0;
        $data['expenses'] = $result->expenses ?? 0;
        $data['revenue']  = $data['income'] - $data['expenses'];
        $data['categories']  = Category::count();
        return view('admin.dashboard', compact('data'));
    }

    public function getAvailableYears()
    {
        $years = Account::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'years' => $years
        ]);
    }

    public function getCategories()
    {
        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function getMonthlyData(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $categoryId = $request->input('category_id');

        // Get category-wise monthly data for the specified year
        $query = Account::selectRaw('
                category_id,
                MONTH(created_at) as month,
                SUM(CASE WHEN type = 1 THEN totalAmount ELSE 0 END) as income,
                SUM(CASE WHEN type = 2 THEN totalAmount ELSE 0 END) as expenses
            ')
            ->with('category:id,name')
            ->whereYear('created_at', $year);

        // Apply category filter if specified
        if ($categoryId && $categoryId != 'all') {
            $query->where('category_id', $categoryId);
        }

        $monthlyData = $query->groupBy('category_id', 'month')
            ->orderBy('category_id')
            ->orderBy('month')
            ->get();

        // Get categories based on filter
        if ($categoryId && $categoryId != 'all') {
            $categories = Category::where('id', $categoryId)->get();
        } else {
            $categories = Category::all();
        }
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Prepare category-wise data structure
        $categoryWiseData = [];
        $totalIncomeByMonth = array_fill(0, 12, 0);
        $totalExpenseByMonth = array_fill(0, 12, 0);
        $totalRevenueByMonth = array_fill(0, 12, 0);

        foreach ($categories as $category) {
            $categoryData = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'months' => []
            ];

            $categoryTotalIncome = 0;
            $categoryTotalExpense = 0;

            for ($i = 0; $i < 12; $i++) {
                $monthData = $monthlyData->where('category_id', $category->id)
                    ->where('month', $i + 1)
                    ->first();

                $income = $monthData ? (float) $monthData->income : 0;
                $expense = $monthData ? (float) $monthData->expenses : 0;
                $profit = $income - $expense;

                $categoryData['months'][] = [
                    'month' => $months[$i],
                    'income' => $income,
                    'expense' => $expense,
                    'profit' => $profit
                ];

                $categoryTotalIncome += $income;
                $categoryTotalExpense += $expense;

                // Add to totals
                $totalIncomeByMonth[$i] += $income;
                $totalExpenseByMonth[$i] += $expense;
                $totalRevenueByMonth[$i] += $profit;
            }

            $categoryData['total_income'] = $categoryTotalIncome;
            $categoryData['total_expense'] = $categoryTotalExpense;
            $categoryData['total_profit'] = $categoryTotalIncome - $categoryTotalExpense;

            $categoryWiseData[] = $categoryData;
        }

        // Overall totals
        $overallTotals = [
            'income' => array_sum($totalIncomeByMonth),
            'expense' => array_sum($totalExpenseByMonth),
            'profit' => array_sum($totalRevenueByMonth)
        ];

        return response()->json([
            'success' => true,
            'year' => $year,
            'months' => $months,
            'categoryWiseData' => $categoryWiseData,
            'totalsByMonth' => [
                'income' => $totalIncomeByMonth,
                'expense' => $totalExpenseByMonth,
                'profit' => $totalRevenueByMonth
            ],
            'overallTotals' => $overallTotals
        ]);
    }


    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('adminLogin');
    }


    public function otpLoad(Request $request)
    {
        $uuID = session('uuid') ?? $request->uuid;
        $admin = Admin::find($uuID);

        if (!$admin) {
            return back()->with([
                'message' => 'User Not Found',
                'alert-type' => 'error'
            ]);
        }

        $randCode = rand(100000,999999);
        $toster = array(
            'message' => 'User Found',
            'alert-type' => 'success'
        );
        $status = storeOtp($admin, $randCode);
        $name = $admin->name;
        $messageContent = "Your Reset Code is : {$randCode}";

        // Email Code
        if($admin->email != null && $status == true)
        {
            Mail::to($admin->email)->queue(new ForgetPassMail($name,$messageContent));
        }
        else
        {
            return back()->with([
                'message' => 'Error in otp sending',
                'alert-type' => 'error'
            ]);
        }

        return view('auth.admin.otp', compact('admin'))->with($toster);

    }

    public function validateOtp(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|digits:1',
        ]);



        if ($validator->fails()) {
            $toster = array(
                'message' => 'Wrong OTP',
                'alert-type' => 'error'
            );
            return redirect()->route('loadForgetMyPass')->with( $toster);
        }

        $otp = preg_replace('/\D/', '', implode('', $request->input('otp')));


        $admin = Admin::find($request->uuid);

        // if ($admin->otp == $request->otp && $admin->otp_validate_time > now())
        if ($admin?->otp == $otp)
        {
            return view('auth.admin.confirmpass', compact('admin'));
        }
        else
        {
            $toster = array(
                'message' => 'Wrong OTP',
                'alert-type' => 'error'
            );

            return back()->with( $toster);
        }
    }

    public function updatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ],
        [
            'password.required' => 'The Password is required',
            'password_confirmation.required' => 'The Confirm Password is required',
            'password_confirmation.same' => 'The Confirm Password and Password must match',
        ]
    );

        if ($validator->fails()) {
            $toster = array(
                'message' => $validator->errors()->first(),
                'alert-type' => 'error'
            );
            return redirect()->route('adminLogin')->with( $toster);
        }


        $admin = Admin::find($request->uuid);
        $admin->password = Hash::make($request->password);
        $admin->save();

        $toster = array(
            'message' => 'Password Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('adminLogin')->with($toster);
    }

}
