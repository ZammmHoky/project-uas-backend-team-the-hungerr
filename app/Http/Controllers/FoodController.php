<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function index()
    {
        return response()->json(Food::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $food = Food::create($request->all());
        return response()->json($food, 201);
    }

    public function show($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($food);
    }

    public function update(Request $request, $id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $food->update($request->all());
        return response()->json($food);
    }

    public function destroy($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $food->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
