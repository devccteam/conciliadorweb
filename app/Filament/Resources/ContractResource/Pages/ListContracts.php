<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalFooterActionsAlignment('end')
                ->mutateFormDataUsing(function (array $data): array {
                    $cnpjCpf = $data['cpf_cnpj'];

                    if (strlen($cnpjCpf) === 11) {
                        $data['contractor_type'] = 'individual';
                    } elseif (strlen($cnpjCpf) === 14) {
                        $data['contractor_type'] = 'company';
                    }
                  
                    return $data;
                })
                ->using(function (array $data): Model {
                    $name = strtoupper($data['name']);
                    
                    $data['name'] = $name . ' - ' . $data['cpf_cnpj'];

                    $contract = static::getModel()::create($data);
                    
                    $user = User::create([
                        'email' => $data['email'],
                        'name' => $name,
                        'password' => bcrypt('password')
                    ]);

                    $role = Role::firstOrCreate([
                        'name' => 'Administrador Contrato',
                    ]);

                    $contract->users()->attach($user->id, [
                        'role_id' => $role->id,
                        'contract_admin' => true,
                    ]);

                    return $contract;
                }),
        ];
    }
}
