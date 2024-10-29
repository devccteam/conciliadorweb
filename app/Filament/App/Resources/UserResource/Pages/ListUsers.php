<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

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
                    $user = static::getModel()::where('email', $data['email'])->first();
                    
                    if(!$user) {
                        $user = static::getModel()::create($data);
                    }

                    $user->contracts()->attach(Filament::getTenant(), [
                        'role_id' => $data['role_id']
                    ]);

                    return $user;
                }),
        ];
    }
}
