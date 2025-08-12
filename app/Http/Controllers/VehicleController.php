<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vehicle;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    use ApiResponses;
    /**
     * Lista os veículos de um cliente.
     */
    public function index($client, Request $request)
    {
        if (!Client::find($client)) {
            return $this->notFound('Cliente não encontrado');
        }

        try {
            $query = Vehicle::where('client_id', $client);

            if ($q = $request->query('q')) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%")
                        ->orWhere('license_plate', 'like', "%{$q}%");
                });
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderBy('brand')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar veículos: ' . $e->getMessage());
            return $this->serverError('Não foi possível listar os veículos');
        }
    }

    /**
     * Cria um novo veículo para o cliente.
     */
    public function store($client, Request $request)
    {
        if (!Client::find($client)) {
            return $this->notFound('Cliente não encontrado');
        }

        try {
            $data = $request->validate([
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer',
                'license_plate' => 'nullable|string|max:20',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        $data['client_id'] = $client;

        try {
            $vehicle = Vehicle::create($data);
            return $this->created($vehicle);
        } catch (\Exception $e) {
            Log::error('Erro ao criar veículo: ' . $e->getMessage());
            return $this->serverError('Não foi possível criar o veículo');
        }
    }

    /**
     * Exibe os detalhes de um veículo específico do cliente.
     */
    public function show($client, $id)
    {
        try {
            $vehicle = Vehicle::where('client_id', $client)
                ->findOrFail($id);
            return $this->success($vehicle);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Veículo não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar veículo: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar o veículo');
        }
    }

    /**
     * Atualiza os dados de um veículo do cliente.
     */
    public function update($client, $id, Request $request)
    {
        try {
            $vehicle = Vehicle::where('client_id', $client)->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Veículo não encontrado');
        }

        try {
            $data = $request->validate([
                'brand' => 'sometimes|required|string|max:255',
                'model' => 'sometimes|required|string|max:255',
                'year' => 'sometimes|required|integer',
                'license_plate' => 'nullable|string|max:20',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $vehicle->update($data);
            return $this->success($vehicle, 'Veículo atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar veículo: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o veículo');
        }
    }

    /**
     * Remove um veículo do cliente.
     */
    public function destroy($client, $id)
    {
        try {
            $vehicle = Vehicle::where('client_id', $client)->findOrFail($id);
            $vehicle->delete();
            return $this->success(null, 'Veículo excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Veículo não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir veículo: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o veículo');
        }
    }
}
