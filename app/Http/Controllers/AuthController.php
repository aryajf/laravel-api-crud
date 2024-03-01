<?php

namespace App\Http\Controllers;

use App\Helpers\Validation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Validation::inputCheck($request->all(), 'Gagal Login', [
            'email' => 'required|email',
            'password' => 'required',
        ],[
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email / Password Tidak sesuai'],
            ]);
        }

        $token = $user->createToken('userlogin', ['*'], now()->addWeek())->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Login Berhasil',
            'token' => $token,
            'data' => $user
        ]);
    }

    public function logout(Request $request){
        $user = request()->user();
        $tokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', $tokenId)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logout Berhasil'
        ]);
    }
    public function profile(Request $request){
        $user = request()->user();
        return response()->json([
            'status' => true,
            'data' => $user,
            'message' => 'Akun berhasil ditampilkan'
        ]);
    }
}
