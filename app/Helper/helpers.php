<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;
use Carbon\Carbon;
use App\Jobs\SendSmsJob;


if (! function_exists('validationError')) {
 function validationError($validator)
 {

            $errors = $validator->errors();
            $errorResponse = [];
            foreach ($errors->messages() as $field => $messages) {
                $errorResponse[] = [
                    'field' => $field,
                    'message' => $messages[0],
                ];
            }
       return $errorResponse;
 }
}
 if (! function_exists('sendSms')) {
    function sendSms($mobile,$message)
    {
        $url = "http://bulksmsbd.net/api/smsapi";
        $api_key = "LOT6oU5qFxxey42Ijqug";
        $senderid = "8809617615215";
        // $mobile = $request->input('mobile'); // Assuming the mobile number is passed in the request
        $msg = $message;   // Assuming the message is passed in the request
        $number = $mobile;

        $response = Http::asForm()->post($url, [
            'api_key' => $api_key,
            'senderid' => $senderid,
            'number' => $number,
            'message' => "কারিকুলাম পোর্টাল থেকে স্বাগতম। আপনার ভেরিফিকেশন কোডটি ".$msg,
        ]);
        Log::debug($response->body());
        if ($response->successful()) {
            // Success
            return "success";
        } else {
            // Error
            return "fail";
        }
    }
}

if (! function_exists('validationMobileNumber')) {
    function validationMobileNumber($mobileNumber,$iso)
    {
        $phone = new PhoneNumber($mobileNumber, $iso);
       $generatedPhone =  $phone->formatForMobileDialingInCountry($iso);
        return  $generatedPhone;
    }
   }

   if (! function_exists('limitVerification')) {
    function limitVerification($user)
    {
        $currentOtpDate = Carbon::parse(Carbon::now()->toDateString()) ;

        $dbOtpDate = Carbon::parse($user->otp_date);
        $code = rand(100000,999999);

        if($currentOtpDate->eq($dbOtpDate))
        {
                if ($user->otp_count <= (int) config('app.max_otp') ?? 10)
                    {
                        $otpValidateTime = (int) config('app.otp_time');
                        $user->otp = $code;
                        $user->otp_validate_time = Carbon::now()->addMinutes($otpValidateTime);
                        $user->save();
                        $user->increment('otp_count');
                        $phoneNumber = validationMobileNumber($user->phone);
                        SendSmsJob::dispatch($phoneNumber, $code);
                        return true;


                    }

                    else
                    {
                        return false;

                    }



        }

        else
        {
            $otpValidateTime = (int)  config('app.otp_time');
            $user->otp = $code;
            $user->otp_validate_time = Carbon::now()->addMinutes($otpValidateTime);
            $user->otp_date =  Carbon::now()->toDateString();
            $user->otp_count = 1;
            $user->save();
            $phoneNumber = validationMobileNumber($user->phone);
            SendSmsJob::dispatch($phoneNumber, $code);
            return true;
        }
    }
   }

   if (! function_exists('storeOtp')) {
    function storeOtp($clientObject,$otp)
    {
        $clientObject->update([
            'otp'=>$otp
        ]);
        return true;
    }
   }
