<?php

namespace App\Filament\Gateway\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SelectContract extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.gateway.pages.select-contract';

    protected static ?string $title = '';

    protected static bool $shouldRegisterNavigation = false;

    public $contracts;

    public function mount()
    {

        $this->contracts = Auth::user()->contracts;

        if ($this->contracts->count() === 1) {
            return redirect('/');
        }
    }
}
