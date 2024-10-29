<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ImportResource\Pages;
use App\Models\Import;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImportResource extends Resource
{
    protected static ?string $model = Import::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Importação';

    protected static ?string $pluralModelLabel = 'Importações';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-play-circle',
                        'completed' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                    }),
                TextColumn::make('layout.name'),
                TextColumn::make('user.name')
                    ->label('Usuário')
                    
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Split::make([
                            Grid::make(12)
                                ->schema([
                                    Group::make()
                                        ->columnSpan(6)
                                        ->schema([
                                            TextEntry::make('user.name')
                                                ->label('Usuário'),
                                            TextEntry::make('layout.name')
                                                ->label('Layout'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'pending' => 'gray',
                                                    'processing' => 'info',
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                }),
                                        ]),
                                    Group::make()
                                        ->columnSpan(6)
                                        ->schema([
                                            TextEntry::make('total_files')
                                                ->label('Arquivos importados'),
                                            TextEntry::make('error_message')
                                                ->label('Mensagem de erro')
                                                ->limit(400)
                                                ->placeholder('Nenhuma mensagem de erro'),
                                        ]),
                                ]),
                        ])->from('lg'),
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
            'index' => Pages\ListImports::route('/'),
            'create' => Pages\CreateImport::route('/create'),
            'edit' => Pages\EditImport::route('/{record}/edit'),
            'view' => Pages\ViewImport::route('/{record}'),
        ];
    }
}
