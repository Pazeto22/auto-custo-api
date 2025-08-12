<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    use ApiResponses;
    /**
     * Lista os orçamentos, podendo filtrar por client_id, vehicle_id ou user_id.
     */
    public function index(Request $request)
    {
        try {
            $query = Budget::query();

            // Filtra por cliente
            if ($clientId = $request->query('client_id')) {
                $query->where('client_id', $clientId);
            }
            // Filtra por veículo
            if ($vehicleId = $request->query('vehicle_id')) {
                $query->where('vehicle_id', $vehicleId);
            }
            // Filtra por usuário
            if ($userId = $request->query('user_id')) {
                $query->where('user_id', $userId);
            }
            // Filtra por status
            if ($status = $request->query('status')) {
                $query->where('status', $status);
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderByDesc('id')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar orçamentos: ' . $e->getMessage());
            return $this->serverError('Erro ao listar orçamentos');
        }
    }

    /**
     * Cria um novo orçamento.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'user_id' => 'required|exists:users,id',
                'status' => 'nullable|in:sketch,pending,approved,rejected',
                'total_price' => 'nullable|numeric|min:0',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $budget = Budget::create($data);
            return $this->created($budget->load(['client', 'vehicle', 'user', 'items']));
        } catch (\Exception $e) {
            Log::error('Erro ao criar orçamento: ' . $e->getMessage());
            return $this->serverError('Erro ao criar orçamento');
        }
    }

    /**
     * Exibe os detalhes de um orçamento específico.
     */
    public function show($id)
    {
        try {
            $budget = Budget::with(['client', 'vehicle', 'user', 'items'])
                ->findOrFail($id);
            return $this->success($budget);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Orçamento não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar o orçamento');
        }
    }

    /**
     * Atualiza os dados de um orçamento.
     */
    public function update(Request $request, $id)
    {
        try {
            $budget = Budget::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Orçamento não encontrado');
        }


        try {
            $data = $request->validate([
                'client_id' => 'sometimes|required|exists:clients,id',
                'vehicle_id' => 'sometimes|required|exists:vehicles,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'status' => 'nullable|in:sketch,pending,approved,rejected',
                'total_price' => 'nullable|numeric|min:0',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $budget->update($data);
            return $this->success($budget->load(['client', 'vehicle', 'user', 'items']), 'Orçamento atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o orçamento');
        }
    }

    /**
     * Remove um orçamento.
     */
    public function destroy($id)
    {
        try {
            $budget = Budget::findOrFail($id);
            $budget->delete();
            return $this->success(null, 'Orçamento excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Orçamento não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o orçamento');
        }
    }
}
