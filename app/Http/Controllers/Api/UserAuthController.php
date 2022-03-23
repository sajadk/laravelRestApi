<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            $token = $user->createToken("LaravelRestApi")->accessToken;
            return response()->json(['user' => auth()->user(),"token" => $token], 200);
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
        $user = User::all();

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
        $this->validate($request, [
            'name' => 'required|min:4',
            'username' => 'required|unique:users,username,'.$id,
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
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
