<?php

namespace App\Providers\Filament;

use App\Filament\App\Resources\UserResource\Pages\ListUsers;
use App\Filament\Gateway\Pages\SelectContract;
use App\Models\Contract;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as FilamentLoginResponse;
use App\Http\Responses\CustomLoginResponse;
use Filament\Navigation\MenuItem;


class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $this->app->singleton(FilamentLoginResponse::class, CustomLoginResponse::class);

        return $panel
            ->id('app')
            ->path('')
            ->tenant(Contract::class)
            ->tenantRoutePrefix('')
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Alterar Contrato')
                    ->url('/gateway/select-contract')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    //->visible(fn (): bool => auth()->user()->contracts->count() > 1),
                // ...

            ])
            // ->tenantMenu(false)
            ->login()
            ->profile()
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->brandName(config('app.name'))
            // ->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/favicon.ico'))
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            // ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s');
    }
}
