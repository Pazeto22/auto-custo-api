<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workshop;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponses;

    /**
     * Exibe uma lista de usuários.
     */
    public function index($workshop, Request $request)
    {
        // Verifica se o workshop existe
        if (!Workshop::find($workshop)) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $query = User::where('workshop_id', $workshop);

            if ($q = $request->query('q')) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderBy('name')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar usuários: ' . $e->getMessage());
            return $this->serverError('Erro ao listar usuários');
        }
    }

    /**
     * Armazena um novo usuário.
     */
    public function store($workshop, Request $request)
    {
        // Verifica se o workshop existe
        if (!Workshop::find($workshop)) {
            return $this->notFound('Oficina não encontrada');
        }

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:30',
                'rank' => 'nullable|in:master,adm,MOD,default',
                'more_information' => 'nullable|string|max:500',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        $data['workshop_id'] = $workshop;
        $data['password'] = bcrypt($data['password']);

        try {
            $user = User::create($data);
            return $this->created($user);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return $this->serverError('Não foi possível criar o usuário');
        }
    }

    /**
     * Exibe os detalhes de um usuário específico.
     */
    public function show($workshop, $id)
    {
        try {
            $user = User::where('workshop_id', $workshop)
                ->with('workshop')
                ->findOrFail($id);
            return $this->success($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Usuário não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar usuário: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar o usuário');
        }
    }

    /**
     * Atualiza os dados de um usuário específico.
     */
    public function update($workshop, $id, Request $request)
    {
        try {
            $user = User::where('workshop_id', $workshop)->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Usuário não encontrado');
        }

        try {
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:30',
                'rank' => 'nullable|in:master,adm,MOD,default',
                'more_information' => 'nullable|string|max:500',
                'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $user->update($data);
            return $this->success($user, 'Usuário atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o usuário');
        }
    }

    /**
     * Deleta um usuário específico.
     */
    public function destroy($workshop, $id)
    {
        try {
            $user = User::where('workshop_id', $workshop)->findOrFail($id);
            $user->delete();
            return $this->success(null, 'Usuário excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Usuário não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir usuário: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o usuário');
        }
    }
}
