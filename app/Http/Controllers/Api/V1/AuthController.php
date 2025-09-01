<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Models\LoginOnDevice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use ApiResponder;
    // Send opt to verify mobile number.
    public function getOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobileNumber' => 'required|min:10|max:10',
            'requestingFor'=>'required',
            'deviceId'=> 'required|min:10|max:255'
        ]);

        if ($validator->fails()) {
            return $this->responseWithError($validator->errors()->all());
        }

        // generate random number
        $random = null;
        $random = rand(100000,999999);

        // save into otps model
        $otp = new Otp;
        $otp->mobileNumber = $request->mobileNumber;
        $otp->requestingFor = $request->requestingFor;
        $otp->deviceId = $request->deviceId;
        $otp->otp = $random;
        $otp->expires_at = Carbon::now()->addMinutes(60);
        $otp->save();

        return $this->responseWithData(
            [
                'mobileNumber'=>$request->mobileNumber,
                'requestingFor'=> $request->requestingFor,
                'deviceId' => $request->deviceId,
                'otp'=>$random,
                'id'=>$otp->id
            ]
        );

    }

    public function verifyOtp(Request $request){
        // validate data
        $validator = Validator::make($request->all(), [
            'mobileNumber' => 'required|min:10|max:10',
            'requestingFor'=>'required',
            'deviceId'=> 'required|min:10|max:255',
            'otp' => 'required|min:6|max:6',
        ]);

        // return if validation failed
        if ($validator->fails()) {
            return $this->responseWithError($validator->errors()->all());
        }

        // get otps shared on requested number
        $otpRecord = null;
        $otpRecord = Otp::where('mobileNumber', $request->mobileNumber)
        ->where('otp', $request->otp)
        ->latest()
        ->first();

        // return back if no otp fouund with requested mobile number
        if (!$otpRecord) {
            return $this->responseWithError('Invalid OTP', $request->all());
        }

        // verify otp validity
        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return $this->responseWithError('OTP has expired.', $request->all());
        }

        // create profile if not exits with request mobile number and device id
        // so basically a profile uniqueness defined by 
        // combinationa of mobile number and device id
        // the combination of this both data stored in email id
        $user = User::where('mobileNumber', $request->mobileNumber)->where('deviceId', $request->deviceId)->first();
        $token = null;

        if (!$user) {
            // Create user if not exists
            $user = User::create([
                'mobileNumber' => $request->mobileNumber,
                'deviceId'=>$request->deviceId,
                'email' => trim($request->mobileNumber.'-'.$request->deviceId),
                'password' => Hash::make($request->mobileNumber), // Random password
            ]);

            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;
        }else{
            // Revoke previous tokens and return a fresh token
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        // add entry into login devices
        $isLoginOnAnyDevice = null;
        $isLoginOnAnyDevice = LoginOnDevice::where('mobileNumber', $request->mobileNumber)
        ->where('deviceId', $request->deviceId)
        ->first();
        
        if(!$isLoginOnAnyDevice){
            // Create entry if not exists
            $loginOnDevice = new LoginOnDevice;
            $loginOnDevice->mobileNumber = $request->mobileNumber;
            $loginOnDevice->deviceId = $request->deviceId;
            $loginOnDevice->deviceName = $request->deviceName;
            $loginOnDevice->deviceModelNo = $request->deviceModelNo;
            $loginOnDevice->save();
        }

        // find total loggedin device
        $totalLoggedinDevice = LoginOnDevice::where('mobileNumber', $request->mobileNumber)->get();

        return $this->responseWithData(
            [
                'user' => $user,
                'token' => $token,
                'totalLoggedinDevice'=> $totalLoggedinDevice
            ],
            'OTP verified successfully.'
        );
    }

    public function logout(Request $request){
        if(!$request->user()){
            return $this->responseWithError('Unauthenticated access', $request->all());
        }
        
        $deleted = false;
        $deleted = $request->user()->tokens()->delete();
        
        if($deleted){
           return $this->responseWithData(
                [
                    'user' => $request->user()
                ],
                'You logged out successfully.'
            ); 
        }else{
            return $this->responseWithError('Trubbling in logout, please try after some time', $request->all());
        }
    }

}
