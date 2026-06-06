<?php

namespace App\Providers;

use App\Filament\Pages\Analytics;
use App\Filament\Pages\DashboardPage;
use App\Filament\Pages\Warehouse;
use App\Filament\Pages\IcebergCube;
use App\Filament\Pages\Prediction;
use App\Filament\Widgets\PriceByCityChart;
use App\Filament\Widgets\PriceByFurnitureChart;
use App\Filament\Widgets\PriceByLegalStatusChart;
use App\Filament\Widgets\PriceSegmentChart;
use App\Filament\Widgets\PropertyStatsOverview;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                DashboardPage::class,
                Analytics::class,
                Warehouse::class,
                IcebergCube::class,
                Prediction::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                PropertyStatsOverview::class,
                PriceSegmentChart::class,
                PriceByCityChart::class,
                PriceByLegalStatusChart::class,
                PriceByFurnitureChart::class,
            ])
            ->navigationGroups([
                'Tổng quan',
                'Quản lý',
                'Phân tích',
            ])
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
            ->brandName('Housing VN')
            ->favicon(asset('favicon.ico'));
    }
}
