<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashInApprovalResource\Pages;
use App\Filament\Resources\CashInApprovalResource\RelationManagers;
use App\Models\CashIn;
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

class CashInApprovalResource extends Resource
{
    protected static ?string $model = CashIn::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

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
        if (\in_array($record->cash_in_status, ['approved', 'rejected'])) {
            return false;
        }

        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('cash_in_approval.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('cash_in_approval.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('cash_in_approval.title_plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('cash_in_status', 'not_approved')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('cash_in_status', 'not_approved')->count();
        return $count > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('cash_in_status', ['not_approved', 'approved', 'rejected']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('receipt_no')
                            ->label(__('cash_in.fields.receipt_no'))
                            ->disabled(),

                        Forms\Components\TextInput::make('pks_no')
                            ->label(__('cash_in.fields.pks_no'))
                            ->disabled(),

                        Forms\Components\TextInput::make('category')
                            ->label(__('cash_in.fields.category'))
                            ->formatStateUsing(fn (string $state): string => __('cash_in.category.' . $state))
                            ->disabled(),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('cash_in.fields.amount'))
                            ->disabled()
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('date')
                            ->label(__('cash_in.fields.date'))
                            ->disabled(),

                        Forms\Components\TextInput::make('partner_name')
                            ->label(__('cash_in.fields.partner_name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('faculty')
                            ->label(__('cash_in.fields.faculty'))
                            ->disabled(),

                        Forms\Components\Select::make('cash_in_status')
                            ->label(__('cash_in.fields.cash_in_status'))
                            ->options([
                                'approved' => __('cash_in.status.approved'),
                                'not_approved' => __('cash_in.status.not_approved'),
                                'rejected' => __('cash_in.status.rejected'),
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record && \in_array($record->cash_in_status, ['approved', 'rejected'])),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label(__('cash_in.fields.receipt_no'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pks_no')
                    ->label(__('cash_in.fields.pks_no'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label(__('cash_in.fields.category'))
                    ->colors([
                        'primary' => 'internal',
                        'success' => 'external',
                    ])
                    ->formatStateUsing(fn (string $state): string => __('cash_in.category.' . $state)),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('cash_in.fields.amount'))
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('cash_in.fields.date'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('partner_name')
                    ->label(__('cash_in.fields.partner_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('faculty')
                    ->label(__('cash_in.fields.faculty'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('cash_in_status')
                    ->label(__('cash_in.fields.cash_in_status'))
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'not_approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => __('cash_in.status.' . $state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cash_in.fields.created_at'))
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
                    ->action(function (CashIn $record) {
                        $filename = 'cash_in_' . str_replace(['/', '\\'], '_', $record->receipt_no) . '_approval.pdf';
                        return response()->streamDownload(function () use ($record) {
                            echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cash-in-approval', ['cashIn' => $record])->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
                Tables\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (CashIn $record) => !\in_array($record->cash_in_status, ['approved', 'rejected']))
                    ->action(function (CashIn $record) {
                        $record->update([
                            'cash_in_status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Cash In approved successfully'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CashIn $record) => !\in_array($record->cash_in_status, ['approved', 'rejected']))
                    ->action(function (CashIn $record) {
                        $record->update([
                            'cash_in_status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Cash In rejected successfully'))
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
                            'cash_in_status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()
                            ->title(__('Cash Ins approved successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Cash In Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('receipt_no')
                            ->label(__('cash_in.fields.receipt_no')),
                        Infolists\Components\TextEntry::make('pks_no')
                            ->label(__('cash_in.fields.pks_no')),
                        Infolists\Components\TextEntry::make('category')
                            ->label(__('cash_in.fields.category'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'internal' => 'primary',
                                'external' => 'success',
                            })
                            ->formatStateUsing(fn (string $state): string => __("cash_in.category.$state")),
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('cash_in.fields.amount'))
                            ->money('IDR', locale: 'id'),
                        Infolists\Components\TextEntry::make('date')
                            ->label(__('cash_in.fields.date'))
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('partner_name')
                            ->label(__('cash_in.fields.partner_name')),
                        Infolists\Components\TextEntry::make('faculty')
                            ->label(__('cash_in.fields.faculty')),
                        Infolists\Components\TextEntry::make('cash_in_status')
                            ->label(__('cash_in.fields.cash_in_status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'not_approved' => 'warning',
                                'rejected' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => __("cash_in.status.$state")),
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
            'index' => Pages\ListCashInApprovals::route('/'),
            'view' => Pages\ViewCashInApproval::route('/{record}'),
        ];
    }
}
