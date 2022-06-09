<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Requests\User\UserRegisterRequest;
use App\Resources\UserDetailResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        try{
            $validatedData =  $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);
            $token = $user->createToken('token')->plainTextToken;
            return sendSuccessResponse('User Registered Successfully',
                [
                    'user'=>  new UserDetailResource($user),
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]);
        }catch(\Exception $e){
            return sendErrorResponse($e->getMessage(),$e->getCode());
        }
    }

    public function login(Request $request)
    {
        try{
           $validatedData =  $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);


            $user = User::where('email', $validatedData['email'])->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid Login Credentials !', 401);
            }

            $token = $user->createToken('token')->plainTextToken;
            return sendSuccessResponse('Authenticated',
                [
                    'userDetail' => new UserDetailResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            );
        }catch(\Exception $e){
            return sendErrorResponse($e->getMessage(),$e->getCode());
        }

    }

    public function test()
    {
        return sendSuccessResponse('Authenticated',
        [
           'user_code' => auth()->id(),
            'name' => 'sandeep'
        ]);


    }
}
