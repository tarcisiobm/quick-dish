<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission; // Importar o Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Para validação

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retorna todas as permissões, com seus grupos de permissão (opcional, mas útil)
        return response()->json(Permission::with('permissionGroup')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Regras de validação
        $validator = Validator::make($request->all(), [
            'permission_group_id' => 'required|integer|exists:permission_groups,id', // Deve existir na tabela permission_groups
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cria uma nova permissão
        $permission = Permission::create($request->all());
        return response()->json($permission, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Encontra a permissão pelo ID, com seu grupo de permissão
        $permission = Permission::with('permissionGroup')->find($id);

        if ($permission) {
            return response()->json($permission, 200);
        }

        return response()->json(['erro' => 'Permissão não encontrada'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Encontra a permissão pelo ID
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['erro' => 'Permissão não encontrada'], 404);
        }

        // Regras de validação
        $validator = Validator::make($request->all(), [
            'permission_group_id' => 'sometimes|required|integer|exists:permission_groups,id',
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Atualiza a permissão
        $permission->update($request->all());
        return response()->json($permission, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Encontra a permissão pelo ID
        $permission = Permission::find($id);

        if ($permission) {
            // Usa soft delete
            $permission->delete();
            return response()->json(null, 204); // 204 No Content
        }

        return response()->json(['erro' => 'Permissão não encontrada'], 404);
    }
}