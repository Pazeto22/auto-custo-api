<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Log;

class WorkshopController extends Controller
{
    use ApiResponses;

    /**
     * Exibe uma lista de oficinas.
     */
    public function index(Request $request)
    {
        try {
            $query = Workshop::query();

            if ($q = $request->query('q')) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%");
                });
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderBy('name')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar oficinas: ' . $e->getMessage());
            return $this->serverError('Erro ao listar oficinas');
        }
    }

    /**
     * Armazena uma nova oficina.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:workshops,email',
                'phone' => 'nullable|string|max:30',
                'address' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $workshop = Workshop::create($data);
            return $this->created($workshop);
        } catch (\Exception $e) {
            Log::error('Erro ao criar oficina: ' . $e->getMessage());
            return $this->serverError('Não foi possível criar a oficina');
        }
    }

    /**
     * Exibe os detalhes de uma oficina específica.
     */
    public function show($id)
    {
        try {
            $workshop = Workshop::findOrFail($id);
            return $this->success($workshop);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Oficina não encontrada');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar oficina: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar a oficina');
        }
    }

    /**
     * Atualiza os dados de uma oficina específica. 
     */
    public function update(Request $request, $id)
    {
        try {
            $workshop = Workshop::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'nullable|email|max:255|unique:workshops,email,' . $workshop->id,
                'phone' => 'nullable|string|max:30',
                'address' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $workshop->update($data);
            return $this->success($workshop, 'Oficina atualizada com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar oficina: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar a oficina');
        }
    }

    /**
     * Deleta uma oficina específica.
     */
    public function destroy($id)
    {
        try {
            $workshop = Workshop::findOrFail($id);
            $workshop->delete();
            return $this->success(null, 'Oficina excluída com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Oficina não encontrada');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir oficina: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir a oficina');
        }
    }
}
