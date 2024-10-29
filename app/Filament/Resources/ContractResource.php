<?php

namespace App\Filament\Resources;

use App\Models\Contract;
use App\Services\DocumentValidatorService;
use App\Filament\Resources\ContractResource\Pages\CreateContract;
use App\Filament\Resources\ContractResource\Pages\EditContract;
use App\Filament\Resources\ContractResource\Pages\ListContracts;
use App\Components\Inputs\CpfCnpjInput;
use App\Filament\Resources\ContractResource\Widgets\ContractUsersWidget;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $modelLabel = 'Contrato';

    protected static ?string $pluralModelLabel = 'Contratos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                static::getFormSchema(),
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Cliente')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cpf_cnpj')
                    ->label('CPF/CNPJ')
                    ->formatStateUsing(function ($state, $record) {
                        return DocumentValidatorService::maskCpfCnpj($state, $record->contractor_type);
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contractor_type')
                    ->label('Tipo contratante')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'individual' => 'Pessoa Fisica',
                            'company' => 'Pessoa Júridica',
                        ];
                        return $types[$state] ?? 'Desconhecido';
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_count')
                    ->label('Qtd. Empresas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_count')
                    ->label('Qtd. Usuarios')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->boolean(),
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
            ])
            ->filters([
                SelectFilter::make('contractor_type')
                    ->label('Tipo Contratante')
                    ->options([
                        'individual' => 'Pessoa Física',
                        'company' => 'Pessoa Jurídica',
                    ])
                    ->placeholder('Todos'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->options([
                        'true' => 'Ativo',
                        'false' => 'Inativo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment('end')
                    ->form(
                        static::getFormSchema()
                    ),
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
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'edit' => EditContract::route('/{record}'),
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make('Dados do Contrato')
                ->columns(12)
                ->schema([
                    Tabs::make('Contract Details')
                        ->columnSpan(12)
                        ->tabs([
                            Tab::make('Empresa')
                                ->schema([
                                    CpfCnpjInput::make('cpf_cnpj', 12),
                                    TextInput::make('corporate_name')
                                        ->columnSpan(12)
                                        ->label('Razão Social')
                                        ->required()
                                        ->validationMessages(['required' => 'A Razão Social é obrigatória.']),
                                ]),

                            Tab::make('Endereço')
                                ->schema([
                                    TextInput::make('street')
                                        ->columnSpan(8)
                                        ->label('Logradouro'),
                                    TextInput::make('number')
                                        ->columnSpan(4)
                                        ->label('Número'),
                                    TextInput::make('state')
                                        ->columnSpan(2)
                                        ->label('Estado'),
                                    TextInput::make('city')
                                        ->columnSpan(6)
                                        ->label('Cidade'),
                                    TextInput::make('neighborhood')
                                        ->columnSpan(4)
                                        ->label('Bairro'),
                                    TextInput::make('complement')
                                        ->columnSpan(12)
                                        ->label('Complemento'),
                                ]),
                        ]),

                    Section::make('Dados do Usuario')
                        ->columns(12)
                        ->schema([
                            TextInput::make('name')
                                ->columnSpan(6)
                                ->label('Nome Cliente')
                                ->required()
                                ->placeholder('PLUSTECH TECNOLOGIA'),
                            TextInput::make('email')
                                ->columnSpan(6)
                                ->label('Email')
                                ->placeholder('plustech@conferire.com')
                                ->required()
                                ->email()
                                ->unique(
                                    fn (string $operation): string => match ($operation) {
                                        'create' => 'users',
                                        'edit' => 'contracts',
                                    },
                                    'email',
                                    ignoreRecord: true,
                                )
                                ->validationMessages([
                                    'required' => 'O email é obrigatório.',
                                    'email' => 'O email deve ser válido.',
                                    'unique' => 'O email já está em uso.',
                                ]),
                            TextInput::make('company_count')
                                ->columnSpan(3)
                                ->numeric()
                                ->label('Qtd. Empresas')
                                ->placeholder('30')
                                ->minValue(1)
                                ->required(),
                            TextInput::make('user_count')
                                ->columnSpan(3)
                                ->numeric()
                                ->label('Qtd. Usuarios')
                                ->placeholder('30')
                                ->minValue(1),
                            Toggle::make('status')
                                ->columnSpan(3)
                                ->label('Ativo')
                                ->inline(false)
                                ->onColor('success')
                                ->offColor('danger')
                                ->default(true),
                        ]),
                ]),
        ];
    }
}
