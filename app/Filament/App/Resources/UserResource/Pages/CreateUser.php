<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

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
        $data['password'] = bcrypt('password');

        $user = static::getModel()::where('email', $data['email'])->first();
                    
        if(!$user) {
            $user = static::getModel()::create($data);
        }

        $user->contracts()->attach(Filament::getTenant(), [
            'role_id' => $data['role_id']
        ]);

        return $user;
    }
}
