<?php

namespace App\Filament\App\Resources\LayoutResource\Pages;

use App\Filament\App\Resources\LayoutResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLayout extends CreateRecord
{
    protected static string $resource = LayoutResource::class;

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
        $tenant = Filament::getTenant();
        
        $data['contract_id'] = $tenant->id;

        $data['format'] = 'Excel';

        return static::getModel()::create($data);
    }
}
