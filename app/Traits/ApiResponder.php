<?php

namespace App\Traits;

trait ApiResponder
{
    // response
    private function responseWithError($error, $requestData = []){
        return response()->json(
            [
                'status'=>false,
                'error' => $error,
                'request'=>$requestData
            ]
            ,200);
    }

    private function responseWithData($data, $message=null){
        return response()->json(
            [
                'status'=>true,
                'message' => $message,
                'data' => $data
            ]
            ,200);
    }
}
