<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Workshop;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    use ApiResponses;

    /**
     * Exibe uma lista de clientes.
     */
    public function index($workshop, Request $request)
    {
        if (!Workshop::find($workshop)) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $query = Client::where('workshop_id', $workshop);

            if ($q = $request->query('q')) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderBy('name')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar clientes: ' . $e->getMessage());
            return $this->serverError('Erro ao listar clientes');
        }
    }

    /**
     * Armazena um novo cliente.
     */
    public function store($workshop, Request $request)
    {
        if (!Workshop::find($workshop)) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255|unique:clients,email',
                'address' => 'nullable|string|max:500',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        $data['workshop_id'] = $workshop;

        try {
            $client = Client::create($data);
            return $this->created($client);
        } catch (\Exception $e) {
            Log::error('Erro ao criar cliente: ' . $e->getMessage());
            return $this->serverError('Não foi possível criar o cliente');
        }
    }

    /**
     * Exibe os detalhes de um cliente específico.
     */
    public function show($workshop, $id)
    {
        try {
            $client = Client::where('workshop_id', $workshop)
                ->with('vehicles', 'budgets')
                ->findOrFail($id);
            return $this->success($client);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Cliente não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar cliente: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar o cliente');
        }
    }

    /**
     * Atualiza os dados de um cliente específico.
     */
    public function update($workshop, $id, Request $request)
    {
        try {
            $client = Client::where('workshop_id', $workshop)->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Cliente não encontrado');
        }

        try {
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255|unique:clients,email,' . $client->id,
                'address' => 'nullable|string|max:500',
                'more_information' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $client->update($data);
            return $this->success($client, 'Cliente atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cliente: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o cliente');
        }
    }

    /**
     * Deleta um cliente específico.
     */
    public function destroy($workshop, $id)
    {
        if (!Workshop::find($workshop)) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $client = Client::where('workshop_id', $workshop)->findOrFail($id);
            $client->delete();
            return $this->success(null, 'Cliente excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Cliente não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir cliente: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o cliente');
        }
    }
}
