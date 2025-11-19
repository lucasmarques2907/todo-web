<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 4);
        return new UserCollection(User::paginate($perPage));
    }

    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|string|min:3|max:20|alpha_num',
            'password' => 'required|string|min:6|max:20|confirmed|alpha_num'
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao realizar cadastro', 'errors' => $validateData->errors()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $user = User::create([
            'username' => $request->username,
            'password' => $request->password
        ]);

        return response()->json(['message' => 'Usuário cadastrado com sucesso', 'user' => $user->toResource()], 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json(['user' => $user->toResource()], 200);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if (str($user->id)->toString() !== $id) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $validateData = Validator::make($request->all(), [
            'username' => "nullable|unique:users,username,$user->id|string|min:3|max:20|alpha_num",
            'password' => "nullable|string|min:6|max:20|confirmed|alpha_num"
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao atualizar dados', 'errors' => $validateData->errors()], 422);
        }

        if (isset($request['password'])) {
            $request['password'] = Hash::make($request['password']);
        }

        $user->update([
            'username' => $request->username ?? $user->username,
            isset($request['password']) ?: 'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Dados atualizados com sucesso', 'user' => $user->toResource()], 200);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();

        if (str($user->id)->toString() !== $id) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $removed = User::destroy($id);

        if (!$removed) return response()->json(['message' => 'Usuário não encontrado'], 404);

        return response()->json(['message' => 'Usuário removido com sucesso'], 200);
    }
}
