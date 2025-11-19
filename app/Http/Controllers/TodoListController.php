<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoListCollection;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoListController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->input('per_page', 4);
        return new TodoListCollection(TodoList::where('user_id', $user->id)->with('items')->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao criar lista', 'errors' => $validateData->errors()], 422);
        }

        $user = Auth::user();

        $list = TodoList::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Lista criada com sucesso', 'list' => $list->toResource()], 201);
    }

    public function show(string $id)
    {

        $user = Auth::user();

        $list = TodoList::where('id', $id)->where('user_id', $user->id)->with('items')->first();

        if (!$list) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        return response()->json([
            'message' => 'Lista encontrada',
            'data' => $list->toResource()
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $validateData = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao atualizar lista', 'errors' => $validateData->errors()], 422);
        }

        $user = Auth::user();

        $list = TodoList::where('id', $id)->where('user_id', $user->id)->first();

        if (!$list) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        $list->update([
            'title' => $request->title ?? $list->title,
            'description' => $request->description ?? $list->description,
        ]);

        return response()->json(['message' => 'Lista atualizada com sucesso', 'list' => $list->toResource()], 200);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();

        $list = TodoList::where('id', $id)->where('user_id', $user->id)->first();

        if (!$list) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        $removed = TodoList::destroy($id);

        if (!$removed) {
            return response()->json(['message' => 'Lista não encontrada'], 404);
        }

        return response()->json(['message' => 'Lista removida com sucesso'], 200);
    }
}
