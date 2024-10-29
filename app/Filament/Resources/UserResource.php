<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\UserResource\Pages;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema(
                static::getFormSchema(),
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
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->sortable()
                    ->boolean(),
                IconColumn::make('is_admin')
                    ->label('Admin.')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->options([
                        'true' => 'Ativo',
                        'false' => 'Inativo',
                    ]),
                SelectFilter::make('is_admin')
                    ->label('Administrador')
                    ->placeholder('Todos')
                    ->options([
                        'true' => 'Sim',
                        'false' => 'Não',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment('end')
                    ->form([
                        Grid::make(12)
                            ->schema(
                                static::getFormSchema()
                            )
                    ])
                    ->mutateFormDataUsing(function (Model $record, array $data): array {
                        $data['password'] = $data['password'] ? bcrypt($data['password']) : $record->password;
                        return $data;
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->columnSpan(fn (string $operation): string => match ($operation) {
                    'create' => 6,
                    'edit' => 12,
                })
                ->label('Nome')
                ->required()
                ->placeholder('João Barbosa'),
            TextInput::make('email')
                ->columnSpan(fn (string $operation): string => match ($operation) {
                    'create' => 5,
                    'edit' => 6,
                })
                ->label('Email')
                ->placeholder('joao.barbosa@example.com')
                ->required()
                ->email()
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'required' => 'O email é obrigatório.',
                    'email' => 'O email deve ser válido.',
                    'unique' => 'O email já está em uso.',
                ]),
            Toggle::make('is_admin')
                ->columnSpan(1)
                ->label('Admin.')
                ->default(false)
                ->inline(false)
                ->onColor('success')
                ->offColor('danger')
                ->onIcon('heroicon-o-bolt')
                ->offIcon('heroicon-o-user')
                ->live()
                ->hiddenOn('edit'),
            TextInput::make('password')
                ->columnSpan(6)
                ->label('Senha')
                ->password()
                ->revealable()
                ->placeholder('********')
                ->hiddenOn('create'),
            Select::make('contract_id')
                ->columnSpan(6)
                ->label('Contratos')
                ->placeholder('Selecione...')
                ->relationship(name: 'contracts', titleAttribute: 'name')
                ->pivotData(fn (Get $get, string $operation) => 
                    ['role_id' => is_array($get('role_id')) ? $get('role_id')[0] : $get('role_id')]
                )
                ->preload()
                ->multiple()
                ->required(fn (Get $get) => !$get('is_admin'))
                ->validationMessages([
                    'required' => 'O contrato é obrigatório.',
                ])
                ->searchable(['cpf_cnpj', 'name'])
                ->hidden(fn (Get $get) => $get('is_admin')),
            Select::make('role_id')
                ->columnSpan(6)
                ->label('Papel')
                ->placeholder('Selecione...')
                ->relationship(name: 'roles', titleAttribute: 'name')
                ->preload()
                ->required(fn (Get $get) => !$get('is_admin'))
                ->validationMessages([
                    'required' => 'O papel é obrigatório.',
                ])
                ->searchable()
                ->hidden(fn (Get $get) => $get('is_admin'))
                ->createOptionForm([
                    TextInput::make('name')
                        ->columnSpan(12)
                        ->label('Nome')
                        ->required()
                        ->placeholder('Administrador')
                        ->unique('roles', 'name')
                        ->validationMessages([
                            'required' => 'O nome é obrigatório.',
                            'unique' => 'O nome já está em uso.',
                        ]),
                ])
                ->createOptionUsing(function ($data) {
                    $role = Role::create($data);

                    return $role->id;
                }),
            Toggle::make('status')
                ->columnSpan(1)
                ->label('Ativo')
                ->inline(false)
                ->onColor('success')
                ->offColor('danger')
                ->hiddenOn('create'),
        ];
    }
}
