<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    //
    public function update(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'current_password' =>'required',
            'new_password' =>'required|min:7|confirmed',
        ]);
        if($validator->fails())
        {
            return response()->json([
                'status'=>false,
                'message'=>'Validation error',
                'errors'=>$validator->errors()
            ],422);
        }
        $user=Auth::user();

        if(!Hash::check($request->current_password,$user->password))
        {
            return response()->json([
                'status'=>false,
                'message'=>'Current Password incorrect'
            ],400);
        }

$user->password=Hash::make($request->new_password);
// $user->save();
return response()->json([
    'status'=>true,
    'message'=>'Password updated Successfully'
]);
    }
}
