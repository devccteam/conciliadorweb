<?php

namespace App\Filament\App\Resources\ImportResource\Pages;

use App\Filament\App\Resources\ImportResource;
use App\Models\ImportRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;



class ViewImport extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ImportResource::class;

    protected static string $view = 'filament.app.resources.import-resource.pages.import-record';

    public function table(Table $table)
    {
        return $table
            ->query(ImportRecord::where('import_id', $this->record->id))
            ->columns([
                TextColumn::make('num_doc')
                    ->label('Nr. Documento')
                    ->toggleable(),
                TextColumn::make('date')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->toggleable(),
                TextColumn::make('client_supplier')
                    ->label('Cliente/Fornecedor')
                    ->toggleable(),
                TextColumn::make('bank')
                    ->limit(10)
                    ->label('Bancos')
                    ->toggleable(),
                TextColumn::make('history')
                    ->label('Histórico')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('debit_value')
                    ->label('Valor Débito')
                    ->toggleable(),
                TextColumn::make('credit_value')
                    ->label('Valor Crédito')
                    ->toggleable(),
                TextColumn::make('interest')
                    ->label('Juros')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fine')
                    ->label('Multa')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discounts')
                    ->label('Descontos')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('other_values')
                    ->label('Outros Valores')
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
