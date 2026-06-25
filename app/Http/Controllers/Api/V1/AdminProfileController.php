<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
// use App\Models\LoginOnDevice;
use App\Http\Controllers\Controller;

class AdminProfileController extends Controller
{
    use ApiResponder;
    
    // get admin profile details
    public function profile(Request $request){
        $user = $request->user();
        $loginDevices = $request->user()->loginDevices()->select('deviceId','deviceName','deviceModelNo')->get();
        if($user){
           return $this->responseWithData(
                [
                    'user' => [
                        'id'            => $user->email,
                        'mobileNumber'  => $user->mobileNumber,
                        'deviceId'      => $user->deviceId,
                        'policyId'      => $user->policyId,
                        'loginDevices'  => $loginDevices
                    ]
                ]
            ); 
        }else{
            return $this->responseWithError('Trubbling.., please try after some time');
        }
    }
    
}
