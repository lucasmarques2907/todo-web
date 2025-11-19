<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:20|alpha_num',
            'password' => 'required|string|min:6|max:20|alpha_num'
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao realizar login', 'errors' => $validateData->errors()], 422);
        }

        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Login realizado com sucesso', 'user' => $user->toResource(), 'token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->toResource());
    }
}
