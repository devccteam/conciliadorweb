<?php

namespace App\Filament\App\Resources\CompanyResource\Pages;

use App\Filament\App\Resources\CompanyResource;
use App\Models\Company;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

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

        if (!$tenant) {
            throw new \Exception('Contract not found');
        }

        $data['contract_id'] = $tenant->id;

        $company = Company::create($data);

        if (isset($data['layout_id'])) {
            $company->layouts()->attach($data['layout_id']);
        }

        return $company;
    }
}
