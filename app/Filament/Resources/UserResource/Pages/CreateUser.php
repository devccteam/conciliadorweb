<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

        $currentUser = Auth::user();

        if ($currentUser->is_admin) {
            if ($data['is_admin']) {
                return static::getModel()::create($data);;
            }
        }

        $data['is_admin'] = false;

        return static::getModel()::create($data);

    }
}
