<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function (Model $record) {
                    $record->load('contracts');
                    $currentContract = Filament::getTenant();

                    if ($currentContract && $record->contracts->contains($currentContract->id)) {
                        $record->contracts()->detach($currentContract->id);
                        return true;
                    }

                    return false;
                }),
            $this->getSaveFormAction()
                ->formId('form'),
            $this->getCancelFormAction(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['password'] = $data['password'] ? bcrypt($data['password']) : $record->password;
            
        $record->update($data);

        return $record;
    }
}
