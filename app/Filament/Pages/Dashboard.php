<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;

use App\Filament\Widgets\WidgetAIncomeChart; // Impor widget
use App\Filament\Widgets\WidgetOrderCountChart; // Impor widget
use App\Filament\Widgets\TopUsersByOrdersWidget; // Impor widget

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Tanggal')
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?? now())
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?? now())
                            ->maxDate(now())
                            ->label('Tanggal Selesai')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            WidgetAIncomeChart::class,
            WidgetOrderCountChart::class,
            TopUsersByOrdersWidget::class,
        ];
    }

    public function applyFiltersToWidgets(): void
    {
        $filters = $this->getFiltersForm()->getState();

        // Contoh: Terapkan filter ke widget (harus disesuaikan dengan logika widget)
        foreach ($this->getWidgets() as $widget) {
            if (method_exists($widget, 'applyFilters')) {
                $widget->applyFilters($filters);
            }
        }
    }
}
