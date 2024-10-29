<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalFooterActionsAlignment('end')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['password'] = bcrypt('password');
                    
                    return $data;
                })
                ->using(function (array $data): Model {
                    $currentUser = Auth::user();

                    if ($currentUser->is_admin) {
                        if ($data['is_admin']) {
                            return static::getModel()::create($data);
                        }
                    }

                    $data['is_admin'] = false;

                    return static::getModel()::create($data);
                }),
        ];
    }
}
