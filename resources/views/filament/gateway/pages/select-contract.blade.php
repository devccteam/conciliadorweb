<x-filament-panels::page>
    <div class="w-full max-w-sm mx-auto p-4 bg-white rounded-lg shadow dark:bg-gray-900 dark:text-white">
        <h2 class="text-2xl font-bold mb-4 text-center">Selecione um contrato</h2>
        
        <div class="filament-forms-field-wrapper">
            <div class="w-full rounded-md border p-4 overflow-y-auto filament-forms-input-wrapper dark:border-gray-700" style="max-height: 200px">
                <ul class="space-y-2">
                    @foreach ($contracts as $contract)
                        <li>
                            <a 
                                href="/{{ $contract->id }}" 
                                class="block p-2 bg-gray-100 rounded-md hover:bg-primary-500 hover:text-white transition-colors dark:bg-gray-700 dark:hover:bg-primary-600"
                            >
                                {{ $contract->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page>
