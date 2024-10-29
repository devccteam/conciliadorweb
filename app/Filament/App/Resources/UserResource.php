<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class UserResource extends Resource

{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Usuário';

    protected static ?string $pluralModelLabel = 'Usuários';

    protected static bool $isScopedToTenant = false;

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
            ->modifyQueryUsing(function (Builder $query) {
                $currentContractId = Filament::getTenant()->id;

                return $query->whereHas('contracts', function ($query) use ($currentContractId) {
                    $query->where('id', $currentContractId)
                        ->where('contract_admin', false);
                });
            })
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment('end')
                    ->form([
                        Grid::make(12)
                            ->schema(
                                static::getFormSchema(),
                            ),
                    ])
                    ->mutateFormDataUsing(function (Model $record, array $data): array {
                        $data['password'] = $data['password'] ? bcrypt($data['password']) : User::find($record['id'])->password;

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->using(function (Model $record) {
                        $record->load('contracts');
                        $currentContract = Filament::getTenant();

                        if ($currentContract && $record->contracts->contains($currentContract->id)) {
                            $record->contracts()->detach($currentContract->id);
                            return true;
                        }

                        return false;
                    }),
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
            // 'edit' => Pages\EditUser::route('/{record}'),
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->columnSpan(6)
                ->label('Nome')
                ->required()
                ->placeholder('João Barbosa'),
            TextInput::make('email')
                ->columnSpan(6)
                ->label('Email')
                ->placeholder('joao.barbosa@example.com')
                ->email()
                ->required()
                ->validationMessages([
                    'email' => 'O email deve ser válido.',
                    'required' => 'O email é obrigatório.',
                ]),
            TextInput::make('password')
                ->columnSpan(6)
                ->label('Senha')
                ->password()
                ->revealable()
                ->placeholder('********')
                ->hiddenOn('create'),
            Select::make('role_id')
                ->columnSpan(6)
                ->label('Papel')
                ->placeholder('Selecione...')
                ->searchable()
                ->relationship(name: 'roles', titleAttribute: 'name')
                ->preload()
                ->required()
                ->validationMessages([
                    'required' => 'O papel é obrigatório.',
                ]),
                
            Toggle::make('status')
                ->columnSpan(6)
                ->label('Ativo')
                ->inline(false)
                ->onColor('success')
                ->offColor('danger')
                ->hiddenOn('create')
        ];
    }
}
