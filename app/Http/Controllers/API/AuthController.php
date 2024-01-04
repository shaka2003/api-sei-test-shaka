<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;


class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'name' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Kesalahan',
                'data' => $validator->errors()
            ]);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['username'] = $input['username'] ?? 'default_username';
        $user = User::create($input);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'message' => 'Sukses Register',
            'data' => $success
        ]);

    }


    public function login(Request $request) {
       if (Auth::attempt(['username' => $request->username, 'password' => $request->password])){
        $auth = Auth::user();
        $success['token'] = $auth->createToken('auth_token')->plainTextToken;
        $success['username'] = $auth->username;
        $success['name'] = $auth->name;
        $success['email'] = $auth->email;

        return response()->json([
            'success' => true,
            'message' => 'Login Sukses',
            'data' => $success
        ]);
       } else {
        return response()->json([
            'success' => false,
            'message' => 'Cek Email dan Password',
            'data' => null
        ]);
       }
    }

    public function getUsers() {
        $users = User::all(['userid', 'username', 'name', 'email']);

        return response()->json([
            'success' => true,
            'message' => 'User list retrieved successfully',
            'data' => $users
        ]);
    }

}
