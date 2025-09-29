<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;
    //
public function getProfile(Request $request)
{
    return $this->successResponse($request->user());
}
public function updateProfile(UpdateProfileRequest $request)
{
    $user =$request->user();
    $validated=$request->validated();
if($request->hasFile('profile_img'))
{
    $path=$request->file('profile_img')->store('avatars','public');
    $validated['profile_img']=$path;
}
$user->update($validated);
return $this->successResponse($user,'Profile Updated Successfully');

}



     public function getNotificationSetting(Request $request)
    {
        return response()->json([
            'notifications_enabled' => $request->user()->notifications_enabled,
        ]);
    }

     public function updateNotificationSetting(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->notifications_enabled = $request->notifications_enabled;
        $user->save();

        return response()->json([
            'message' => 'Notification preference updated successfully',
            'notifications_enabled' => $user->notifications_enabled,
        ]);
    }

    public function deleteProfile(Request $request)
{
    $user = $request->user();


//Password
    if ($request->filled('password')) {
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Password is incorrect', 403);
        }
    }

//Relationss
    $user->doctor()->delete();
    $user->patient()->delete();
    $user->tokens()->delete();
    $user->mobileWallets()->delete();

    $user->delete();

    return $this->successResponse(null, 'Account deleted successfully');
}
}
