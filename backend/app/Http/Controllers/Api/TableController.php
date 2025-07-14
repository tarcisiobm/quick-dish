<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table; // Importa o modelo Table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Exibe uma lista de todas as mesas.
     * GET /api/tables
     */
    public function index()
    {
        $tables = Table::all(); // Ou Table::where('status', true)->get() para apenas mesas ativas

        return response()->json($tables);
    }

    /**
     * Armazena uma nova mesa no banco de dados.
     * POST /api/tables
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|integer|unique:tables,number', // Número da mesa único e obrigatório
            'capacity' => 'required|integer|min:1', // Capacidade obrigatória, mínimo 1
            'status' => 'boolean', // Opcional, padrão é true na migration
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Erro de validação
        }

        try {
            $table = Table::create($request->all()); // Cria a mesa com os dados validados
            return response()->json($table, 201); // 201 Created
        } catch (\Exception $e) {
            \Log::error('Erro ao criar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao criar a mesa.'], 500);
        }
    }

    /**
     * Exibe uma mesa específica.
     * GET /api/tables/{id}
     */
    public function show(string $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404); // 404 Not Found
        }

        return response()->json($table);
    }

    /**
     * Atualiza uma mesa existente no banco de dados.
     * PUT/PATCH /api/tables/{id}
     */
    public function update(Request $request, string $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'sometimes|integer|unique:tables,number,' . $id, // Único, exceto para a própria mesa
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $table->update($request->all()); // Atualiza a mesa
            return response()->json($table); // Retorna a mesa atualizada
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao atualizar a mesa.'], 500);
        }
    }

    /**
     * Remove uma mesa do banco de dados (soft delete).
     * DELETE /api/tables/{id}
     */
    public function destroy(string $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['message' => 'Mesa não encontrada.'], 404);
        }

        try {
            $table->delete(); // Realiza o soft delete (coloca um timestamp em deleted_at)
            return response()->json(['message' => 'Mesa deletada com sucesso.'], 204); // 204 No Content
        } catch (\Exception $e) {
            \Log::error('Erro ao deletar mesa: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor ao deletar a mesa.'], 500);
        }
    }
}