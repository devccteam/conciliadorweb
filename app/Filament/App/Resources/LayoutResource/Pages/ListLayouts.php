<?php

namespace App\Filament\App\Resources\LayoutResource\Pages;

use App\Filament\App\Resources\LayoutResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;

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
            
                    $data['format'] = 'Excel';
            
                    return $data;
                })
        ];
    }
}
