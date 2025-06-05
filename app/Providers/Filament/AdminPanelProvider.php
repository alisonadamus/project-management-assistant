<?php

namespace Alison\ProjectManagementAssistant\Providers\Filament;


use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource;
use Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource;
use Alison\ProjectManagementAssistant\Http\Middleware\CheckAdminAccess;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\MenuItem;

use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => $this->getPrimaryColor(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                CheckAdminAccess::class,
            ])
            ->authGuard('sanctum')
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn (): string => auth()->user()->full_name)
                    ->url(fn (): string => route('profile.show'))
                    ->icon('heroicon-m-user-circle'),
                'dashboard' => MenuItem::make()
                    ->label('Головна сторінка')
                    ->url(fn (): string => route('dashboard'))
                    ->icon('heroicon-m-home')
                    ->openUrlInNewTab(),
                'colors' => MenuItem::make()
                    ->label('Налаштування кольорів')
                    ->url(fn (): string => \Alison\ProjectManagementAssistant\Filament\Pages\ColorSettings::getUrl())
                    ->icon('heroicon-m-paint-brush'),
                'separator' => MenuItem::make()
                    ->label('')
                    ->url('#'),
            ]);
    }

    protected function getPrimaryColor(): array
    {
        $colorName = Cache::get('admin_primary_color', 'amber');

        $colors = [
            'slate' => Color::Slate,
            'gray' => Color::Gray,
            'zinc' => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone' => Color::Stone,
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
        ];

        return $colors[$colorName] ?? Color::Amber;
    }
}
