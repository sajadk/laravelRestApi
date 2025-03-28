<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            "username" => 'required|unique:users,username',
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "username" => $request->username,
            "dob" => $request->dob,
            "phone" => $request->phone,
        ]);

        $token = $user->createToken("LaravelRestApi")->accessToken;

        return response()->json(
            [
                "data" => [
                    "type" => "activities",
                    "message" => "Success",
                    "data" => $token,
                ],
            ],
            200
        );
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {   
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        // $this->validate($request, [
        //     'email' => 'required|email',
        //     'password' => 'required|min:8',
        // ]);

        if ($validator->fails()) {
            return response()->json([
              'errors' => $validator->errors(),
              'status' => Response::HTTP_BAD_REQUEST,
            ], Response::HTTP_BAD_REQUEST);
        }


        $data = [
            "email" => $request->email,
            "password" => $request->password,
        ];

        if (Auth::attempt($data)) {
            $user = Auth::user();
            $tokenResult = $user->createToken("LaravelRestApi");
            $token = $tokenResult->accessToken;
            $tokenExpiry = Carbon::parse($tokenResult->token->expires_at)->timestamp; 
            return response()->json(
                ['user' => auth()->user(),
                "token" => $token,
                'expires_in' => $tokenExpiry - now()->timestamp], 200);
        } else {
            return response()->json(["error" => "Unauthorised"], 401);
        }
    }

     public function logout (Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function userInfo()
    {
        $user = new UserCollection(User::all());

       // var_dump($user);
        return response()->json(
            [
                "data" => [
                    "type" => "activities",
                    "message" => "Success",
                    "data" => $user,
                ],
            ],
            200
        );
    }

     public function userData($id)
    {
        $user = User::find($id);

       // var_dump($user);
        return response()->json($user,200);
    }

    public function update(Request $request, $id)
    {
        //var_dump($id); exit();
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'username' => 'required|unique:users,username,'.$id,
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'nullable|min:5', // Make password optional for updates
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->dob = $request->dob;
        $user->username = $request->username;
        $user->update();
        return response()->json(
            [
                "data" => [
                    "type" => "activities",
                    "message" => "Success",
                    "data" => $user,
                ],
            ],
            200
        );
    }
    public function delete(Request $request, $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(
            [
                "data" => [
                    "type" => "activities",
                    "message" => "Success",
                    "data" => "deleted!",
                ],
            ],
            200
        );
    }
}
