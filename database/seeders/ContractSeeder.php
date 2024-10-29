<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cnpj = 37106016000193;
        $data = Http::get("https://receitaws.com.br/v1/cnpj/{$cnpj}")->throw()->json();

        $contract = [
            'contractor_type' => 'company',
            'corporate_name' => $data['nome'] ?? 'PLUSTECH TECNOLOGIA',
            'street' => $data['logradouro'] ?? 'R JOAO DA CRUZ',
            'city' => $data['municipio'] ?? 'VITORIA',
            'neighborhood' => $data['bairro'] ?? 'PRAIA DO CANTO',
            'complement' => $data['complemento'] ?? 'SALA 401 EDIF TRADE POINT',
            'state' => $data['uf'] ?? 'ES',
            'number' => $data['numero'] ?? '25',
            'activity_branch' => $data['atividade_principal'][0]['text'] ?? 'Suporte técnico, manutenção e outros serviços em tecnologia da informação (Dispensada *)',
            'cpf_cnpj' => $cnpj,
            'name' => 'PLUSTECH TECNOLOGIA',
            'email' => 'plustech@conferire.com',
            'company_count' => 100,
        ];

        $contract = Contract::create($contract);
        
        $user = User::create([
            'name' => $contract->name,
            'email' => $contract->email,
            'password' => bcrypt('password'),
        ]);
        
        $role = Role::firstOrCreate([
            'name' => 'Administrador Contrato',
        ]);

        $contract->users()->attach($user->id, [
            'role_id' => $role->id,
            'contract_admin' => true,
        ]);
    }
}
