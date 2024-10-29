<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Models\Role;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    public function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->formId('form'),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $cnpjCpf = $data['cpf_cnpj'];

        if (strlen($cnpjCpf) === 11) {
            $data['contractor_type'] = 'individual';
        } elseif (strlen($cnpjCpf) === 14) {
            $data['contractor_type'] = 'company';
        }

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
    }
}
