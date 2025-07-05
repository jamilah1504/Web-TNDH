<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Laporan Pembayaran';
    protected static ?string $pluralLabel = 'Laporan Pembayaran';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->disabled(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled(),
                TextInput::make('amount')
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),
                Select::make('status')
                    ->options(['completed' => 'Completed', 'failed' => 'Failed'])
                    ->required(),
                TextInput::make('payment_date')
                    ->type('datetime-local')
                    ->disabled(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')->label('Order ID'),
                TextColumn::make('user.name')->sortable()->searchable(),
                TextColumn::make('amount')->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('status'),
                TextColumn::make('payment_date')->dateTime(),
            ]);
            
            // ->actions([
            //     Action::make('print')
            //         ->label('Print')
            //         ->icon('heroicon-o-printer')
            //         ->action(function ($record) {
            //             return static::printPayment($record);
            //         }),
            // ])
            // ->bulkActions([
            //     Tables\Actions\DeleteBulkAction::make(),
            //     BulkAction::make('print_all')
            //         ->label('Print All Payments')
            //         ->icon('heroicon-o-printer')
            //         ->action(function () {
            //             return static::printAllPayments();
            //         }),
            //     BulkAction::make('download_selected')
            //         ->label('Download Selected Payments')
            //         ->icon('heroicon-o-document-arrow-down')
            //         ->action(function ($records) {
            //             return static::downloadSelectedPayments($records);
            //         }),
            // ])
            // ->headerActions([
            //     Action::make('download_pdf')
            //         ->label('Download PDF')
            //         ->icon('heroicon-o-document-arrow-down')
            //         ->color('primary')
            //         ->tooltip('Download all payments as PDF')
            //         ->requiresConfirmation()
            //         ->action(function () {
            //             return static::downloadAllPayments();
            //         }),
            // ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Disable create action
    }

    /**
     * Generate and stream a PDF for a single payment
     */
    public static function printPayment($record)
    {
        try {
            \Log::info('Printing payment ID: ' . $record->id);
            if (!View::exists('payments.print')) {
                \Log::error('View payments.print not found');
                return redirect()->back()->with('error', 'View payments.print not found');
            }
            $pdf = Pdf::loadView('payments.print', ['payment' => $record]);
            return $pdf->stream('payment-' . $record->id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Print payment failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate and stream a PDF for all payments
     */
    public static function printAllPayments()
    {
        try {
            \Log::info('Printing all payments');
            if (!View::exists('payments.print_all')) {
                \Log::error('View payments.print_all not found');
                return redirect()->back()->with('error', 'View payments.print_all not found');
            }
            $payments = Payment::all();
            if ($payments->isEmpty()) {
                \Log::warning('No payments found for PDF generation');
                return redirect()->back()->with('error', 'No payments available to generate PDF');
            }
            $pdf = Pdf::loadView('payments.print_all', ['payments' => $payments]);
            return $pdf->stream('all-payments.pdf');
        } catch (\Exception $e) {
            \Log::error('Print all payments failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download a PDF for all payments
     */
    public static function downloadAllPayments()
    {
        try {
            \Log::info('Downloading all payments');
            if (!View::exists('payments.print_all')) {
                \Log::error('View payments.print_all not found');
                return redirect()->back()->with('error', 'View payments.print_all not found');
            }
            $payments = Payment::all();
            if ($payments->isEmpty()) {
                \Log::warning('No payments found for PDF generation');
                return redirect()->back()->with('error', 'No payments available to generate PDF');
            }
            $pdf = Pdf::loadView('payments.print_all', ['payments' => $payments]);
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'all-payments.pdf');
        } catch (\Exception $e) {
            \Log::error('Download all payments failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download a PDF for selected payments
     */
    public static function downloadSelectedPayments($records)
    {
        try {
            \Log::info('Downloading selected payments: ' . count($records));
            if (!View::exists('payments.print_all')) {
                \Log::error('View payments.print_all not found');
                return redirect()->back()->with('error', 'View payments.print_all not found');
            }
            if (empty($records)) {
                \Log::warning('No payments selected for PDF generation');
                return redirect()->back()->with('error', 'No payments selected to generate PDF');
            }
            $pdf = Pdf::loadView('payments.print_all', ['payments' => $records]);
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'selected-payments.pdf');
        } catch (\Exception $e) {
            \Log::error('Download selected payments failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}