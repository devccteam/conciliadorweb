<?php

namespace App\Filament\App\Resources\ImportResource\Pages;

use App\Filament\App\Resources\ImportResource;
use App\Imports\RecordsImport;
use App\Models\Import;
use App\Models\ImportSession;
use App\Models\Layout;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ListImports extends ListRecords
{
    protected static string $resource = ImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Importar arquivo')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Grid::make(12)->schema([
                        Select::make('layout_id')
                            ->columnSpan(6)
                            ->label('Layout de importação')
                            ->placeholder('Selecione...')
                            ->options(Layout::where('contract_id', Filament::getTenant()->id)->get()->pluck('name', 'id'))
                            ->required()
                            ->validationMessages([
                                'required' => 'O layout de importação é obrigatório.',
                            ]),

                        FileUpload::make('attachments')
                            ->columnSpan(12)
                            ->label('Arquivo Excel')
                            ->hint('Aguarde até os arquivos serem carregados e o botão "Enviar" ficar disponível.')
                            ->required()
                            ->multiple()
                            ->storeFileNamesIn('attachment_file_names')
                            ->validationMessages([
                                'required' => 'O arquivo é obrigatório.',
                            ]),
                    ]),
                ])
                ->action(function (array $data) {
                    // Check if the layout exists
                    $layout = Layout::findOrFail($data['layout_id']);

                    if (!$layout) {
                        Notification::make()
                            ->title('Layout de importação não encontrado.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Check if the attachments exist
                    if (!$data['attachments']) {
                        Notification::make()
                            ->title('Ocorreu um erro ao importar, por favor, tente novamente.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Create the import
                    $import = Import::create([
                        'name' => 'IMP - ' . Date('d-m-Y H:i:s') . ' - ' . count($data['attachments']) . ' arquivos',
                        'layout_id' => $layout->id,
                        'user_id' => auth()->id(),
                        'status' => 'processing',
                        'total_files' => count($data['attachments']),
                        'contract_id' => Filament::getTenant()->id,
                    ]);

                    DB::beginTransaction();

                    try {
                        // Import each attachment
                        foreach ($data['attachments'] as $attachment) {
                            $importSession = ImportSession::create([
                                'import_id' => $import->id,
                                'file_name' => $data['attachment_file_names'][$attachment],
                                'size' => filesize(storage_path('app/public/' . $attachment)),
                            ]);

                            // Uncomment the line below to test an error
                            //throw new \Exception('Teste de erro');
                            
                            // Import the file
                            Excel::import(new RecordsImport($layout, $importSession), storage_path('app/public/' . $attachment));
                        }

                        // Commit the transaction
                        DB::commit();

                        $import->update(['status' => 'completed']);

                        Notification::make()
                            ->title('Arquivo importado com sucesso!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();

                        // Send a notification with the error message
                        Notification::make()
                            ->title('Erro ao importar o Arquivo Excel')
                            ->body('Erro gerado: ' . $e->getMessage())
                            ->warning()
                            ->send();

                        // Update the import status to 'failed'
                        $import->update([
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                }),
        ];
    }
}
