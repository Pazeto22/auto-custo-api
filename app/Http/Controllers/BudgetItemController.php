<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetItemController extends Controller
{
    use ApiResponses;
    /**
     * Lista os itens de um orçamento.
     */
    public function index($budget)
    {
        try {
            $budgetModel = Budget::with(['items.product'])->findOrFail($budget);
            return $this->success($budgetModel->items);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Orçamento não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao listar itens do orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível listar os itens do orçamento');
        }
    }

    /**
     * Cria um novo item para o orçamento.
     */
    public function store($budget, Request $request)
    {
        if (!Budget::find($budget)) {
            return $this->notFound('Orçamento não encontrado');
        }

        try {
            $data = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        $data['budget_id'] = $budget;

        try {
            $item = BudgetItem::create($data);
            return $this->created($item->load('product'));
        } catch (\Exception $e) {
            Log::error('Erro ao criar item do orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível criar o item do orçamento');
        }
    }

    /**
     * Exibe um item específico de um orçamento.
     */
    public function show($budget, $item)
    {
        try {
            $budgetItem = BudgetItem::where('budget_id', $budget)
                ->with('product')
                ->find($item);
            return $this->success($budgetItem);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Item do orçamento não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao buscar item do orçamento: ' . $e->getMessage());
            return $this->serverError('Erro ao buscar item do orçamento');
        }
    }

    /**
     * Atualiza um item de um orçamento.
     */
    public function update($budget, $item, Request $request)
    {
        try {
            $budgetItem = BudgetItem::where('budget_id', $budget)->findOrFail($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Item do orçamento não encontrado');
        }

        try {
            $data = $request->validate([
                'product_id' => 'sometimes|required|exists:products,id',
                'quantity' => 'sometimes|required|integer|min:1',
                'unit_price' => 'sometimes|required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        }

        try {
            $budgetItem->update($data);
            return $this->success($budgetItem->load('product'), 'Item do orçamento atualizado com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar item do orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível atualizar o item do orçamento');
        }
    }

    /**
     * Remove um item de um orçamento.
     */
    public function destroy($budget, $item)
    {
        try {
            $budgetItem = BudgetItem::where('budget_id', $budget)->findOrFail($item);
            $budgetItem->delete();
            return $this->success(null, 'Item do orçamento excluído com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Item do orçamento não encontrado');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir item do orçamento: ' . $e->getMessage());
            return $this->serverError('Não foi possível excluir o item do orçamento');
        }
    }
}
