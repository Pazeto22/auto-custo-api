<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\User;
use App\Models\Budget;
use App\Models\Product;
use App\Models\BudgetItem;

class AutocustoSeeder extends Seeder
{
  public function run()
  {
    // Criar oficina
    $workshop = Workshop::create([
      'name' => 'AutoCusto Oficina',
      'phone' => '11999999999',
      'address' => 'Rua das Flores, 123',
    ]);

    // Criar usuário da oficina
    $user = User::create([
      'workshop_id' => $workshop->id,
      'name' => 'Gustavo',
      'phone' => '11988888888',
      'rank' => 'adm',
      'email' => 'pazeto95@gmail.com',
      'password' => bcrypt('123456'),
      'more_information' => null,
    ]);

    // Criar cliente
    $client = Client::create([
      'name' => 'João',
      'phone' => '123456789',
      'email' => 'joao@email.com',
      'address' => 'Rua das Acácias, 456',
      'more_information' => 'Cliente VIP',
    ]);

    // Criar veículo para o cliente
    $vehicle = Vehicle::create([
      'client_id' => $client->id,
      'brand' => 'Ford',
      'model' => 'Fiesta',
      'year' => 2010,
      'license_plate' => 'ABC1234',
      'more_information' => 'Carro de uso diário',
    ]);

    // Criar produtos (serviço e peça)
    $service = Product::create([
      'type' => 'service',
      'name' => 'Limpeza',
      'description' => 'Limpeza completa do veículo',
      'price' => 99.99,
    ]);

    $part = Product::create([
      'type' => 'part',
      'name' => 'Filtro de óleo',
      'description' => 'Filtro de óleo original Ford',
      'price' => 49.50,
    ]);

    // Criar orçamento
    $budget = Budget::create([
      'client_id' => $client->id,
      'vehicle_id' => $vehicle->id,
      'user_id' => $user->id,
      'status' => 'sketch',
      'total_price' => 0,
      'more_information' => 'Orçamento inicial para manutenção',
    ]);

    // Criar itens do orçamento
    $item1 = BudgetItem::create([
      'budget_id' => $budget->id,
      'product_id' => $service->id,
      'quantity' => 1,
      'unit_price' => $service->price,
      'total_price' => $service->price * 1,
    ]);

    $item2 = BudgetItem::create([
      'budget_id' => $budget->id,
      'product_id' => $part->id,
      'quantity' => 2,
      'unit_price' => $part->price,
      'total_price' => $part->price * 2,
    ]);

    // Atualizar o total_price do orçamento somando os itens
    $budget->total_price = $budget->items()->sum('total_price');
    $budget->save();
  }
}
