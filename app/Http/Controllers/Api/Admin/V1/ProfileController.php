<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use ApiResponder;
    protected $user = null;
    
    // get admin profile details
    public function show(Request $request){
        $this->user = $request->user();
        return $this->responseWithData(
            [
                'user' => $this->getUserDetails()
            ]
        ); 
    }
    public function update(Request $request){
        $this->user = $request->user();
        $this->user->email = $request->email ? $request->email : $this->user->email;
        $this->user->save();
        return $this->responseWithData(
            ['user' => $this->getUserDetails()],
            'Profile updated successfully!'
        ); 
        
    }

    protected function getUserDetails(){
        return [
            'id'            => $this->user->id,
            'mobileNumber'  => $this->user->mobileNumber,
            'email'         => $this->user->email,
            'deviceId'      => $this->user->deviceId,
            'policyId'      => $this->user->policyId,
            'loginDevices' => $this->getLoginDevices()
        ];
    }

    protected function getLoginDevices(){
        return $this->user->loginDevices()->select('deviceId','deviceName','deviceModelNo')->get();
    }
    
}
