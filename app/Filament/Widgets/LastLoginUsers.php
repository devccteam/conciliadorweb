<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LastLoginUsers extends BaseWidget
{
    protected static ?string $heading = 'Usuários';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => User::query()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nome'),
                IconColumn::make('status')
                    ->sortable()
                    ->boolean(),
            ]);
    }
}
