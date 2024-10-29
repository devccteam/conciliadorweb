<?php

namespace App\Filament\App\Resources\CompanyResource\Pages;

use App\Filament\App\Resources\CompanyResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('7xl')
                ->closeModalByClickingAway(false)
                ->modalFooterActionsAlignment('end')
                ->mutateFormDataUsing(function (array $data): array {
                    $tenant = Filament::getTenant();

                    $data['contract_id'] = $tenant->id;

                    return $data;
                })
        ];
    }
}
