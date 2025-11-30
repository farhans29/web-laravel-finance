<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalResource\Pages;
use App\Filament\Resources\ApprovalResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovalResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['verificator', 'auditor', 'super_admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        // Only verificator and super_admin can edit (approve/reject)
        if (!auth()->user()->hasRole(['verificator', 'super_admin'])) {
            return false;
        }

        // Prevent editing if already approved or rejected
        if (\in_array($record->invoice_status, ['approved', 'rejected'])) {
            return false;
        }

        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('approval.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('approval.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('approval.title_plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('invoice_status', 'not_approved')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('invoice_status', 'not_approved')->count();
        return $count > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('invoice_status', ['not_approved', 'approved', 'rejected']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('invoice_no')
                            ->label(__('invoice.fields.invoice_no'))
                            ->disabled(),

                        Forms\Components\TextInput::make('name')
                            ->label(__('invoice.fields.name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('partner')
                            ->label(__('invoice.fields.partner'))
                            ->disabled(),

                        Forms\Components\TextInput::make('activity_name')
                            ->label(__('invoice.fields.activity_name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('virtual_account_no')
                            ->label(__('invoice.fields.virtual_account_no'))
                            ->disabled(),

                        Forms\Components\TextInput::make('bill')
                            ->label(__('invoice.fields.bill'))
                            ->disabled()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('invoice_status')
                            ->label(__('invoice.fields.invoice_status'))
                            ->options([
                                'approved' => __('invoice.status.approved'),
                                'not_approved' => __('invoice.status.not_approved'),
                                'rejected' => __('invoice.status.rejected'),
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record && \in_array($record->invoice_status, ['approved', 'rejected'])),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label(__('invoice.fields.invoice_no'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('invoice.fields.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('partner')
                    ->label(__('invoice.fields.partner'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('activity_name')
                    ->label(__('invoice.fields.activity_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bill')
                    ->label(__('invoice.fields.bill'))
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('invoice_status')
                    ->label(__('invoice.fields.invoice_status'))
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'not_approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => __('invoice.status.' . $state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('invoice.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label(__('Print PDF'))
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(function (Invoice $record) {
                        $filename = 'invoice_' . str_replace(['/', '\\'], '_', $record->invoice_no) . '_approval.pdf';
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.approval', ['invoice' => $record])->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
                Tables\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record) => !\in_array($record->invoice_status, ['approved', 'rejected']))
                    ->action(function (Invoice $record) {
                        $record->update([
                            'invoice_status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Invoice approved successfully'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record) => !\in_array($record->invoice_status, ['approved', 'rejected']))
                    ->action(function (Invoice $record) {
                        $record->update([
                            'invoice_status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Invoice rejected successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_bulk')
                    ->label(__('Approve Selected'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each->update([
                            'invoice_status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Invoices approved successfully'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\BulkAction::make('pdf_bulk')
                    ->label(__('Export PDFs'))
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        // For bulk export, we'll create a ZIP file with multiple PDFs
                        // For now, let's download the first selected invoice as a sample
                        $firstRecord = $records->first();
                        return response()->streamDownload(function () use ($firstRecord) {
                            echo Pdf::loadView('pdf.approval', ['invoice' => $firstRecord])->output();
                        }, 'invoice_' . $firstRecord->invoice_no . '_approval.pdf', [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Invoice Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_no')
                            ->label(__('invoice.fields.invoice_no')),
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('invoice.fields.name')),
                        Infolists\Components\TextEntry::make('partner')
                            ->label(__('invoice.fields.partner')),
                        Infolists\Components\TextEntry::make('activity_name')
                            ->label(__('invoice.fields.activity_name')),
                        Infolists\Components\TextEntry::make('virtual_account_no')
                            ->label(__('invoice.fields.virtual_account_no')),
                        Infolists\Components\TextEntry::make('bill')
                            ->label(__('invoice.fields.bill'))
                            ->money('IDR', locale: 'id'),
                        Infolists\Components\TextEntry::make('invoice_status')
                            ->label(__('invoice.fields.invoice_status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'not_approved' => 'warning',
                                'rejected' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => __("invoice.status.$state")),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Tracking Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Created By')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/m/Y H:i', 'Asia/Jakarta')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('updater.name')
                            ->label('Updated By')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d/m/Y H:i', 'Asia/Jakarta')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('Approved By')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime('d/m/Y H:i', 'Asia/Jakarta')
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListApprovals::route('/'),
            'view' => Pages\ViewApproval::route('/{record}'),
        ];
    }
}