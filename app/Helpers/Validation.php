<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
class Validation{
    public static function inputCheck($request, $message = '', $rules = [], $customMessage = []){
        $validation = Validator::make($request, $rules, $customMessage);

        if($validation->fails()){
            response()->json([
                'status' => false,
                'message' => $message,
                'errors' => $validation->errors()
            ], 400)->send();
            die;
        }
    }
}
