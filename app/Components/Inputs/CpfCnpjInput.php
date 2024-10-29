<?php

namespace App\Components\Inputs;

use App\Services\DocumentValidatorService;
use Closure;
use Exception;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Http;
use Filament\Support\RawJs;

class CpfCnpjInput
{
    public static function make(string $name, array | int | string | Closure | null $span): TextInput
    {
        return TextInput::make($name)
            ->columnSpan($span)
            ->label('CPF/CNPJ')
            ->required()
            ->placeholder('00.000.000/0000-00')
            ->mask(RawJs::make(
                <<<'JS'
                    $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                JS
            ))
            ->stripCharacters(['.', '/', '-', ','])
            ->formatStateUsing(function ($state, $record) {
                if (blank($state)) {
                    return null;
                }

                return DocumentValidatorService::maskCpfCnpj($state, $record->contractor_type ?? null);
            })
            ->rules([
                'regex:/^\d{11}$|^\d{14}$/',
                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                    $validator = new DocumentValidatorService();
                    if (strlen($value) === 11 && !$validator->isValidDocument($value)) {
                        $fail("O CPF informado é inválido.");
                    } elseif (strlen($value) === 14 && !$validator->isValidDocument($value)) {
                        $fail("O CNPJ informado é inválido.");
                    }
                },
            ])
            ->unique(ignoreRecord: true)
            ->validationMessages([
                'required' => 'O CPF/CNPJ é obrigatório.',
                'unique' => 'O CPF/CNPJ já está em uso.',
                'regex' => 'O CPF/CNPJ deve ter 11 ou 14 dígitos.',
            ])
            ->validationAttribute('CPF/CNPJ')
            ->hint(new HtmlString(Blade::render(
                <<<'BLADE'
                    <div wire:loading wire:target="data.cpf_cnpj" class="flex items-center">
                        <span class="flex items-center space-x-2">
                            <span class="text-primary-400">Buscando dados...</span>
                            <x-filament::loading-indicator class="h-5 w-5 text-primary-400" />   
                        </span>
                    </div>
                BLADE
            )))
            ->live(debounce: 500)
            ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                if (!blank($state) && $state !== $old && strlen($state) > 17) {
                    try {
                        $cnpj = str_replace(['.', '-', '/'], "", $state);
                        $data = Http::get("https://receitaws.com.br/v1/cnpj/{$cnpj}")->throw()->json();

                        if (isset($data['error'])) {
                            throw new Exception("CNPJ não encontrado ou inválido.");
                        }

                        $set('corporate_name', $data['nome'] ?? null);
                        $set('street', $data['logradouro'] ?? null);
                        $set('city', $data['municipio'] ?? null);
                        $set('neighborhood', $data['bairro'] ?? null);
                        $set('complement', $data['complemento'] ?? null);
                        $set('state', $data['uf'] ?? null);
                        $set('number', $data['numero'] ?? null);
                        $set('activity_branch', $data['atividade_principal'][0]['text'] ?? null);

                    } catch (Exception $e) {
                        Notification::make()
                            ->title("Erro ao buscar o CNPJ informado: " . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }
            });
    }
}
