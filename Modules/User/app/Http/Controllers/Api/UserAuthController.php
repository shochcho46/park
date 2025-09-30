<?php

namespace Modules\User\Http\Controllers\Api;

use App\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Transformers\UserResource;

class UserAuthController extends Controller
{
    use ApiResponseTrait;
    // public function adminLoginValidation(Request $request)
    public function userLoginValidation(Request $request)
    {

        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);
        $password = $request->input('password');
        $remember = $request->has('remember');

        $loginInputType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        if ($loginInputType == "email") {
            $user = User::where('email',$request->login_id)->first();
        }
        else{
            $phoneNumber = validationMobileNumber($request->login_id, $request->country_id);
            $user = User::where('phone',$phoneNumber)->first();
        }

        if ($user) {
            if (Hash::check($password, $user->password)) {
                if ($user->status == 1) {
                    $token = $user->createToken('joker')->accessToken;
                    return response()->json(
                        [
                            "user" => new UserResource($user),
                            "token" => $token,

                        ],
                        200
                    );


                } else {
                    return $this->errorResponse("This user is not active", 422);
                }
            } else {
                return $this->errorResponse("Password mismatch", 422);
            }
        }

        return $this->errorResponse("Wrong credential", 422);
    }

    public function userLogout(Request $request)
    {

        Auth::guard('api')->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function userDetail(Request $request)
    {
        if ($request->filled('user_id')) {
            $data = User::findOrFail($request->user_id);
        }
        else{
            $data = Auth::guard('api')->user();
        }

        return $this->successResponse(new UserResource($data), "User Detail", 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
