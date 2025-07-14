<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TableRequest;
use Illuminate\Http\JsonResponse;

class TableController extends ApiController
{
    protected string $model = Table::class;
    protected string $name = 'Table';
    protected ?string $formRequest = TableRequest::class;

    public function index(): JsonResponse
    {
        $tables = Table::all();
        return response()->json($tables);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|integer|unique:tables,number',
            'capacity' => 'required|integer|min:1',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $table = Table::create($request->all());
            return response()->json($table, 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao criar a mesa.'], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $table = Table::find($id);
        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404);
        }
        return response()->json($table);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $table = Table::find($id);
        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'sometimes|integer|unique:tables,number,' . $id,
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $table->update($request->all());
            return response()->json($table);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao atualizar a mesa.'], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $table = Table::find($id);
        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404);
        }
        try {
            $table->delete();
            return response()->json(['message' => 'Mesa deletada com sucesso.'], 204);
        } catch (\Exception $e) {
            \Log::error('Erro ao deletar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao deletar a mesa.'], 500);
        }
    }
}
