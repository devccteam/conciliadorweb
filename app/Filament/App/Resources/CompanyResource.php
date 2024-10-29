<?php

namespace App\Filament\App\Resources;

use Filament\Tables;
use App\Models\User;
use App\Models\Company;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\App\Resources\CompanyResource\Pages;
use App\Components\Inputs\CpfCnpjInput;
use App\Models\Layout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $tenantRelationshipName = 'Company';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Group::make()
                    ->columnspan(10)
                    ->schema(
                        static::getFormSchema()
                    ),
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Histórico')
                            ->columns(12)
                            ->schema([
                                Placeholder::make('created_at')
                                    ->columnSpan(12)
                                    ->label('Criado em')
                                    ->content(fn (?Company $record): string => $record ? $record->created_at?->format('d/m/Y H:i:s') ?? '-' : '-'),
                                Placeholder::make('updated_at')
                                    ->columnSpan(12)
                                    ->label('Alterado em')
                                    ->content(fn (?Company $record): string => $record ? $record->updated_at?->format('d/m/Y H:i:s') ?? '-' : '-'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cpf_cnpj')
                    ->label('CPF/CNPJ')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $document = preg_replace('/[^\d]/', '', $state);
                        if (strlen($document) === 11) {
                            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document);
                        } elseif (strlen($document) === 14) {
                            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document);
                        }

                        return $state;
                    }),
                TextColumn::make('corporate_name')
                    ->label('Razão Social')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),
                TextColumn::make('tax_regime')
                    ->label('Tributação')
                    ->searchable(),
                IconColumn::make('require_approver')
                    ->label('Requer Aprovador?')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('tax_regime')
                    ->label('Regime Tributário')
                    ->options([
                        'Lucro Real' => 'Lucro Real',
                        'Lucro Presumido' => 'Lucro Presumido',
                        'Simples Nacional' => 'Simples Nacional',
                        'Outros' => 'Outros',
                    ])
                    ->placeholder('Todos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('7xl')
                    ->closeModalByClickingAway(false)
                    ->modalFooterActionsAlignment('end')
                    ->form([
                        Grid::make(12)
                            ->schema([
                                Group::make()
                                    ->columnspan(10)
                                    ->schema(
                                        static::getFormSchema()
                                    ),
                                Group::make()
                                    ->columnSpan(2)
                                    ->schema([
                                        Section::make('Histórico')
                                            ->columns(12)
                                            ->schema([
                                                Placeholder::make('created_at')
                                                    ->columnSpan(12)
                                                    ->label('Criado em')
                                                    ->content(fn (?Company $record): string => $record ? $record->created_at?->format('d/m/Y H:i:s') ?? '-' : '-'),
                                                Placeholder::make('updated_at')
                                                    ->columnSpan(12)
                                                    ->label('Alterado em')
                                                    ->content(fn (?Company $record): string => $record ? $record->updated_at?->format('d/m/Y H:i:s') ?? '-' : '-'),
                                            ]),
                                ]),
                            ]),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum Registro Encontrado');
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make('Dados da Empresa')
                ->columns(12)
                ->schema([
                    Tabs::make('Detalhes da empresa')
                        ->columnspan(12)
                        ->tabs([
                            Tab::make('Empresa')
                                ->columns(12)
                                ->schema([
                                    TextInput::make('code')
                                        ->columnSpan(1)
                                        ->label('Código')
                                        ->required()
                                        ->placeholder('1')
                                        ->unique(ignoreRecord: true)
                                        ->validationMessages([
                                            'required' => 'O Codigo é obrigatório.',
                                            'unique' => 'O Codigo já está em uso.',
                                        ])
                                        ->extraInputAttributes(['class' => 'text-lg font-bold']),
                                    CpfCnpjInput::make('cpf_cnpj', 11),
                                    TextInput::make('corporate_name')
                                        ->columnSpan(12)
                                        ->label('Razão Social')
                                        ->required()
                                        ->validationMessages(['required' => 'A Razão Social é obrigatória.']),
                                ]),

                            Tab::make('Endereço')
                                ->schema([
                                    TextInput::make('street')
                                        ->columnSpan(7)
                                        ->label('Logradouro'),
                                    TextInput::make('number')
                                        ->columnSpan(5)
                                        ->label('Número'),
                                    TextInput::make('state')
                                        ->columnSpan(2)
                                        ->label('Estado'),
                                    TextInput::make('city')
                                        ->columnSpan(5)
                                        ->label('Cidade'),
                                    TextInput::make('neighborhood')
                                        ->columnSpan(5)
                                        ->label('Bairro'),
                                    TextInput::make('complement')
                                        ->columnSpan(7)
                                        ->label('Complemento'),
                                    Select::make('tax_regime')
                                        ->columnSpan(5)
                                        ->options([
                                            'Lucro Real' => 'Lucro Real',
                                            'Lucro Presumido' => 'Lucro Presumido',
                                            'Simples Nacional' => 'Simples Nacional',
                                            'Outros' => 'Outros',
                                        ])
                                        ->label('Regime Tributário')
                                        ->placeholder('Selecione...')
                                        ->required()
                                        ->validationMessages(['required' => 'O Regime Tributário é obrigatório.']),
                                    TextInput::make('activity_branch')
                                        ->columnSpan(12)
                                        ->label('Ramo de Atividade'),
                                ]),
                        ]),

                        Tabs::make('Informações Fiscais')
                            ->columnSpan(12)
                            ->tabs([
                                Tab::make('Informações Gerais')
                                    ->columns(12)
                                    ->schema([
                                        Toggle::make('require_approver')
                                            ->columnSpan(2)
                                            ->label('Requer Aprovador')
                                            ->inline(false)
                                            ->live(),
                                        Toggle::make('require_documents')
                                            ->columnSpan(2)
                                            ->label('Anexar Documentos')
                                            ->inline(false),
                                        Toggle::make('has_observations')
                                            ->columnSpan(2)
                                            ->label('Observações')
                                            ->inline(false),
                                        Toggle::make('has_checked_field')
                                            ->columnSpan(2)
                                            ->label('Campo Verificado')
                                            ->inline(false),
                                        Select::make('approver_user_id')
                                            ->columnSpan(6)
                                            ->label('Aprovador')
                                            ->placeholder('Selecione...')
                                            ->options(function () {
                                                $currentContract = Filament::getTenant();
                                                $currentContractId = $currentContract->id ?? null;

                                                if ($currentContractId) {
                                                    return User::whereHas('contracts', function ($query) use ($currentContractId) {
                                                        $query->where('contracts.id', $currentContractId);
                                                    })->pluck('name', 'id');
                                                }

                                                return [];
                                            })
                                            ->searchable(['name'])
                                            ->required(fn ($get) => $get('require_approver'))
                                            ->validationMessages(['required' =>  'Você precisa selecionar um aprovador quando esta opção é habilitada.'])
                                            ->visible(fn ($get) => $get('require_approver')),
                                    ]),

                                Tab::make('Informações Adicionais')
                                    ->columns(12)
                                    ->schema([
                                        Toggle::make('approx_value_enabled')
                                            ->columnSpan(2)
                                            ->label('Valor Aproximado')
                                            ->inline(false)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if (!$state) {
                                                    $set('approx_value_percentage', null);
                                                }
                                            }),
                                        Toggle::make('has_fixed_account')
                                            ->columnSpan(2)
                                            ->label('Conta Fixa?')
                                            ->inline(false)
                                            ->reactive(),
                                        Grid::make(12)
                                            ->schema([
                                                TextInput::make('approx_value_percentage')
                                                    ->columnSpan(3)
                                                    ->label('Porcentagem do Valor Aproximado')
                                                    ->numeric()
                                                    ->required(fn ($get) => $get('approx_value_enabled'))
                                                    ->disabled(fn ($get) => !$get('approx_value_enabled'))
                                                    ->validationMessages([
                                                        'required' => 'A porcentagem do valor aproximado é obrigatória quando o valor aproximado está habilitado.',
                                                        'max' => 'O Valor maximo é de 100%.',
                                                        'min' => 'O Valor minímo é de 1%.',
                                                    ])
                                                    ->rule('min:1')
                                                    ->rule('max:100')
                                                    ->placeholder('%'),
                                                TextInput::make('active_interest_account')
                                                    ->columnSpan(2)
                                                    ->label('Conta Ativo de Juros')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('has_fixed_account'))
                                                    ->validationMessages(['required' => 'A Conta Ativo de Juros é obrigatória quando a conta fixa está habilitada.']),
                                                TextInput::make('passive_interest_account')
                                                    ->columnSpan(2)
                                                    ->label('Conta Passivo de Juros')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('has_fixed_account'))
                                                    ->validationMessages(['required' => 'A Conta Passivo de Juros é obrigatória quando a conta fixa está habilitada.']),
                                                TextInput::make('discounts_obtained_account')
                                                    ->columnSpan(2)
                                                    ->label('Conta Descontos Obtidos')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('has_fixed_account'))
                                                    ->validationMessages(['required' => 'A Conta Descontos Obtidos é obrigatória quando a conta fixa está habilitada.']),
                                                TextInput::make('discounts_given_account')
                                                    ->columnSpan(2)
                                                    ->label('Conta Descontos Dados')
                                                    ->numeric()
                                                    ->visible(fn ($get) => $get('has_fixed_account'))
                                                    ->validationMessages(['required' => 'A Conta Ativo de Juros é obrigatória quando a conta fixa está habilitada.']),
                                            ])
                                    ]),
                                Tab::make('Layouts')
                                    ->Columns(12)
                                    ->Schema([
                                        Select::make('layout_id')
                                            ->columnSpan(6)
                                            ->label('Layouts de Importação')
                                            ->relationship('layouts')
                                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->code} - {$record->movement_type}")
                                            ->multiple()
                                            ->preload()
                                            ->searchable(['code', 'name']),

                                        Select::make('export') // TODO Adicionar a migration
                                            ->columnSpan(6)
                                            ->label('Layout de Exportação')
                                            ->options([
                                                'DOMINIO' => 'DOMINIO',
                                                'ALTERDATA' => 'ALTERDATA',
                                            ])
                                            ->preload()
                                            ->nullable(),
                                    ])
                            ]),
                ]),
        ];
    }
}
