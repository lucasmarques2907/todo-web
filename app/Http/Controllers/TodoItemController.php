<?php

namespace App\Http\Controllers;

use App\Models\TodoItem;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoItemController extends Controller
{
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'list_id' => 'required|integer|exists:lists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao criar item', 'errors' => $validateData->errors()], 422);
        }

        $user = Auth::user();

        $list = TodoList::where('id', $request->list_id)->where('user_id', $user->id)->first();

        if (!$list) {
            return response()->json(['message' => 'A lista não pertence ao usuário autenticado'], 403);
        }

        $item = TodoItem::create([
            'list_id' => $list->id,
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Item criado com sucesso', 'item' => $item->toResource()], 201);
    }

    public function update(Request $request, string $id)
    {

        $validateData = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'completed' => 'nullable|boolean'
        ]);

        if ($validateData->fails()) {
            return response()->json(['message' => 'Falha ao atualizar item', 'errors' => $validateData->errors()], 422);
        }

        $user = Auth::user();

        $item = TodoItem::where('id', $id)
            ->whereHas('list', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        $item->update([
            'title' => $request->title ?? $item->title,
            'description' => $request->description ?? $item->description,
            'completed' => $request->completed ?? $item->completed
        ]);

        return response()->json(['message' => 'Item atualizado com sucesso', 'item' => $item->toResource()], 200);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();

        $item = TodoItem::where('id', $id)
            ->whereHas('list', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        $removed = TodoItem::destroy($id);

        if (!$removed) {
            return response()->json(['message' => 'Item não encontrado'], 404);
        }

        return response()->json(['message' => 'Item removido com sucesso'], 200);
    }
}
