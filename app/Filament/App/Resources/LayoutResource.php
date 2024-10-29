<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LayoutResource\Pages;
use App\Models\Layout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema(
                static::getFormSchema()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Código'),
                TextColumn::make('sector')
                    ->label('Setor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('movement_type')
                    ->label('Tipo de Movimento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Alterado em')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_default_layout')
                    ->label('Padrão')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('sector')
                    ->label('Setor')
                    ->options([
                        'Contábil' => 'Contábil',
                        'Fiscal' => 'Fiscal',
                    ]),
                SelectFilter::make('movement_type')
                    ->label('Tipo de Movimento')
                    ->options([
                        'Ambos' => 'Ambos',
                        'Pagar' => 'Pagar',
                        'Receber' => 'Receber',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('7xl')
                    ->modalFooterActionsAlignment('end')
                    ->form([
                        Grid::make(12)
                            ->schema(
                                static::getFormSchema(),
                            ),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }
    
    public static function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns(12)
                ->compact()
                ->schema([
                    TextInput::make('name')
                        ->columnSpan(12)
                        ->label('Nome')
                        ->required()
                        ->validationMessages(['required' => 'O campo nome é obrigatório']),
                    Select::make('format')
                        ->columnSpan(4)
                        ->label('Formato')
                        ->placeholder('Selecione...')
                        ->options([
                            'Excel' => 'Excel',
                        ])
                        ->default('Excel')
                        ->disabled()
                        ->dehydrated(false)
                        ->required()
                        ->validationMessages(['required' => 'O campo formato é obrigatório']),
                    Select::make('sector')
                        ->columnSpan(4)
                        ->label('Setor')
                        ->placeholder('Selecione...')
                        ->options([
                            'Contábil' => 'Contábil',
                            'Fiscal' => 'Fiscal',
                        ])
                        ->required()
                        ->validationMessages(['required' => 'O campo setor é obrigatório']),
                    Select::make('movement_type')
                        ->columnSpan(4)
                        ->label('Tipo de Movimento')
                        ->placeholder('Selecione...')
                        ->options([
                            'Ambos' => 'Ambos',
                            'Pagar' => 'Pagar',
                            'Receber' => 'Receber',
                        ])
                        ->required()
                        ->validationMessages(['required' => 'O campo tipo de movimento é obrigatório']),
                ]),
            // Section::make()
            //     ->columns(12)
            //     ->compact()
                //->schema([
                    Group::make()
                        ->columnspan(7)
                        ->schema([
                            Section::make('Colunas')
                                ->columns(12)
                                ->compact()
                                ->schema([
                                    TextInput::make('start_row')
                                        ->columnSpan(4)
                                        ->label('Linha Inicial')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->validationMessages(['required' => 'O campo linha inicial é obrigatório']),
                                    TextInput::make('num_doc_column')
                                        ->columnSpan(4)
                                        ->label('Nr. Documento')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('parcel_separator')
                                        ->columnSpan(4)
                                        ->label('Separador Parcela')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('date_column')
                                        ->columnSpan(4)
                                        ->label('Data')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state))
                                        ->required()
                                        ->validationMessages(['required' => 'O campo data é obrigatório']),
                                    TextInput::make('history_column')
                                        ->columnSpan(4)
                                        ->label('Histórico')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state))
                                        ->required()
                                        ->validationMessages(['required' => 'O campo histórico é obrigatório']),
                                    TextInput::make('history_2_lines_column')
                                        ->columnSpan(4)
                                        ->label('Histórico 2 Linhas')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('debit_value_column')
                                        ->columnSpan(4)
                                        ->label('Valor Débito')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('credit_value_column')  
                                        ->columnSpan(4)
                                        ->label('Valor Crédito')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('interest_column')
                                        ->columnSpan(4)
                                        ->label('Juros')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('fine_column')
                                        ->columnSpan(4)
                                        ->label('Multa')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('discounts_column')
                                        ->columnSpan(4)
                                        ->label('Descontos')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('other_values_column')
                                        ->columnSpan(4)
                                        ->label('Outros Valores')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('ignore_history')
                                        ->columnSpan(4)
                                        ->label('Ignorar Hist/Ad com')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('client_supplier_column')
                                        ->columnSpan(4)
                                        ->label('Cliente/Fornecedor')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('debit_credit_column')
                                        ->columnSpan(4)
                                        ->label('D/C')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                    TextInput::make('bank_column')
                                        ->columnSpan(4)
                                        ->label('Banco')
                                        ->dehydrateStateUsing(fn (?string $state): ?string => strtoupper($state)),
                                ]),
                        ]),
                    Group::make()
                        ->columnspan(5)
                        ->schema([
                            Section::make('Configurações de Importação')
                                ->columns(12)
                                ->compact()
                                ->schema([
                                    Toggle::make('consider_previous_date')
                                        ->columnSpan(12)
                                        ->label('Considerar data anterior ao encontrar em branco'),
                                    Toggle::make('consider_previous_client_supplier')
                                        ->columnSpan(12)
                                        ->label('Considerar cliente/fornecedor anterior ao encontrar em branco'),
                                    Toggle::make('consider_previous_history')
                                        ->columnSpan(12)
                                        ->label('Considerar histórico anterior ao encontrar em branco'),
                                    Toggle::make('consider_previous_bank')
                                        ->columnSpan(12)
                                        ->label('Considerar banco anterior ao encontrar em branco'),
                                    Toggle::make('is_default_layout')
                                        ->columnSpan(12)
                                        ->label('Indica se o layout é o padrão'),
                                ]),
                        ]),
                //]),
        ];
    }
}
