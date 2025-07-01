<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 

class PermissionGroupController extends Controller
{
   
    public function index()
    {
       
        return response()->json(PermissionGroup::all(), 200);
    }

   
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150', 
            'description' => 'nullable|string|max:255', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permissionGroup = PermissionGroup::create($request->all());
        return response()->json($permissionGroup, 201); 
    }

    
    public function show(string $id)
    {
        $permissionGroup = PermissionGroup::find($id);

        if ($permissionGroup) {
            return response()->json($permissionGroup, 200);
        }

        return response()->json(['erro' => 'Grupo de Permissão não encontrado'], 404);
    }

    
    public function update(Request $request, string $id)
    {
        
        $permissionGroup = PermissionGroup::find($id);

        if (!$permissionGroup) {
            return response()->json(['erro' => 'Grupo de Permissão não encontrado'], 404);
        }

       
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

      
        $permissionGroup->update($request->all());
        return response()->json($permissionGroup, 200);
    }

   
    public function destroy(string $id)
    {
        $permissionGroup = PermissionGroup::find($id);

        if ($permissionGroup) {
            $permissionGroup->delete();
            return response()->json(null, 204); 
        }

        return response()->json(['erro' => 'Grupo de Permissão não encontrado'], 404);
    }
}