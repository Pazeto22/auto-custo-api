<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponses;
    /**
     * Lista os produtos.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Busca por nome ou descrição
            if ($q = $request->query('q')) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }

            // Filtro por tipo
            if ($type = $request->query('type')) {
                $query->where('type', $type);
            }

            // Filtro por faixa de preço
            if ($min = $request->query('min_price')) {
                $query->where('price', '>=', $min);
            }
            if ($max = $request->query('max_price')) {
                $query->where('price', '<=', $max);
            }

            $perPage = (int) $request->query('per_page', 15);
            return $this->success($query->orderBy('name')->paginate($perPage));
        } catch (\Exception $e) {
            Log::error('Erro ao listar produtos: ' . $e->getMessage());
            return $this->serverError('Não foi possível listar os produtos');
        }
    }

    /**
     * Cria um novo produto.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'type' => 'required|in:service,part',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $product = Product::create($data);
            return $this->created($product);
        } catch (\Exception $e) {
            Log::error('Erro ao criar produto: ' . $e->getMessage());
            return $this->serverError('Erro ao criar produto');
        }
    }

    /**
     * Exibe os detalhes de um produto específico.
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return $this->success($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Produto não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar produto: ' . $e->getMessage());
            return $this->serverError('Não foi possível buscar o produto');
        }
    }

    /**
     * Atualiza os dados de um produto.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Produto não encontrado');
        }

        try {
            $data = $request->validate([
                'type' => 'sometimes|required|in:service,part',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $product->update($data);
            return $this->success($product, 'Produto atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o produto');
        }
    }

    /**
     * Remove um produto.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return $this->success(null, 'Produto excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Produto não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o produto');
        }
    }
}
