<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPassMail;
use App\Models\Country;
use App\Models\Gender;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator ;

class HomeController extends Controller
{

    public function home(Request $request)
    {

        return view('frontend.pages.home');
    }

    public function dashboard(Request $request)
    {

        return view('user.dashboard');
    }

    public function login()
    {
        $datas = Country::all();
        $genders = Gender::all();
        return view('auth.login',compact('datas','genders'));

    }




    public function registration()
    {
        $datas = Country::all();
        $genders = Gender::all();
        return view('auth.register',compact('datas','genders'));

    }


    public function validateLogin(Request $request)
    {
        $countryIso = Country::where('id',18)->first();

        $validated = $request->validate([
            // 'email_or_phone' => ['bail','required','regex:/^[0-9+]+$/',(new Phone)->country([$countryIso->iso])],
            'email_or_phone' => ['bail','required'],

            'password' => 'required',
            ],
            [
                'email_or_phone.regex' => 'The phone number must contain only English digits (0-9).',
                'email_or_phone.required' => 'The phone number is required',
            ]
        );


        $password = $request->input('password');

        if (filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email_or_phone)
            // ->orWhere('phone', $phoneNumber)
            ->first();
        }
        else
        {
            $phoneNumber = validationMobileNumber($request->email_or_phone,$countryIso->iso);
            $user = User::where('email', $request->email_or_phone)
                    ->orWhere('phone', $phoneNumber)
                    ->first();
        }



        if ($user) {
            if (Hash::check($password, $user->password))
            {

                if (($user->status == 0)) {


                    $toster = array(
                        'message' => "This account is in black listed",
                        'alert-type' => 'error'
                    );
                    return back()->with( $toster);

                } else {

                    if ($request->has('remember')) {
                        Auth::guard('web')->login($user, true);
                    } else {
                        Auth::guard('web')->login($user);
                    }
                    $toster = array(
                        'message' => "Wlecome to Dashboard, ".$user->name,
                        'alert-type' => 'success'
                    );

                    return redirect()->route('user.dashboard')->with( $toster);
                }

            }

            else
            {
                return back()->with('fail', 'Wrong Credential');
            }
        }
        else
        {
            $toster = array(
                'message' => "User Not Found",
                'alert-type' => 'error'
            );

            return back()->with( $toster);
        }
    }





    public function storRegistration(Request $request)
    {
        $code = rand(100000,999999);
        $countryID = $request->country_id ?? 18;
        $countryIso = Country::where('id',$countryID)->first();


        $validated = $request->validate([
            'name' => 'required',
            'email' => 'unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required|same:password',
            // 'education_type_id' => 'required',
            'phone' => ['required','unique:users','regex:/^[0-9+]+$/',(new Phone)->country([$countryIso->iso]??['BD']),],
            // 'upazila_id' => 'required',
            // 'district_id' => 'required',
            // 'division_id' => 'required',
            'gender_id' => 'required',
            // 'country_id' => 'required',
            ],
            [
                'phone.regex' => 'The phone number must contain only English digits (0-9).',
                'phone.required' => 'The phone number is required',
            ]
        );


        $phoneNumber = validationMobileNumber($request->phone,$countryIso->iso);

            $user = DB::transaction(function () use($request,$code,$phoneNumber) {
                $userCreate = array(
                    "name" => $request->name,
                    "email" => $request->email ?? null,
                    "password" => Hash::make($request->password),
                    "phone" => $phoneNumber,
                    "otp" => $code,
                    "status" => 1,

                );

                $newuser = User::create($userCreate);

                $userdetail = array(
                    "user_id" => $newuser->id,
                    "division_id" => $request->division_id ?? null,
                    "district_id" => $request->district_id ?? null ,
                    "upazila_id" => $request->upazila_id ?? null ,
                    "union_id" => $request->union_id ?? null ,
                    "education_type_id" => $request->education_type_id ?? null ,
                    "profession_id" => $request->profession_id ?? null ,
                    "gender_id" => $request->gender_id ?? null ,
                    "country_id" => $request->country_id ?? null ,
                    "religion_id" => $request->religion_id ?? null ,
                );
                $userDetail = UserDetail::create($userdetail);

                return $newuser;
            });

            if ($user->status == 1) {
                $toster = array(
                    'message' => "Registration Successfull",
                    'alert-type' => 'success'
                );
                return redirect()->route('login')->with( $toster);

            } else {

                $toster = array(
                    'message' => "Registration Fail",
                    'alert-type' => 'error'
                );
                return redirect()->route('registration')->with( $toster);

            }
    }


    public function googleOauthLoad()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleOauthCallBack()
    {
        $user = Socialite::driver('google')->user();
        dd( $user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::route('login');
    }

    public function loadForgetMyPass()
    {
        $datas = Country::all();
        return view('auth.forgetpass',compact('datas'));

    }

    public function searchUser(Request $request)
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

        $user = User::where($credential)->first();

        if ($user) {
            $toster = array(
                'message' => 'User Found',
                'alert-type' => 'success'
            );

            return redirect()->route('userOtpLoad')->with('uuid', $user->id)->with($toster);

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


    public function userOtpLoad(Request $request)
    {
        $uuID = session('uuid') ?? $request->uuid;
        $user = User::find($uuID);

        if (!$user) {
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
        $status = storeOtp($user, $randCode);
        $name = $user->name;
        $messageContent = "Your Reset Code is : {$randCode}";

        // Email Code
        if($user->email != null && $status == true)
        {
            Mail::to($user->email)->queue(new ForgetPassMail($name,$messageContent));
        }
        else
        {
            return back()->with([
                'message' => 'Error in otp sending',
                'alert-type' => 'error'
            ]);
        }

        return view('auth.userotp', compact('user'))->with($toster);


    }


    public function validateUserOtp(Request $request)
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
            return redirect()->route('forgetMyPass')->with( $toster);
        }

        $otp = preg_replace('/\D/', '', implode('', $request->input('otp')));


        $user = User::find($request->uuid);

        // if ($admin->otp == $request->otp && $admin->otp_validate_time > now())
        if ($user?->otp == $otp)
        {
            $toster = array(
                'message' => 'Otp Matched',
                'alert-type' => 'success'
            );
            return view('auth.passconfirm', compact('user'))->with($toster);
        }
        else
        {
            $toster = array(
                'message' => 'Wrong OTP',
                'alert-type' => 'error'
            );

            return redirect()->route('userOtpLoad')->with('uuid', $user->id)->with($toster);
            // return view('auth.userotp', compact('user'))->with($toster);

        }
    }



    public function updateUserPassword(Request $request)
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
            return redirect()->route('login')->with( $toster);
        }


        $user = User::find($request->uuid);
        $user->password = Hash::make($request->password);
        $user->save();

        $toster = array(
            'message' => 'Password Updated',
            'alert-type' => 'success'
        );

        return redirect()->route('login')->with($toster);
    }
}
