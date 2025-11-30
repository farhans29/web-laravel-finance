<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashInResource\Pages;
use App\Filament\Resources\CashInResource\RelationManagers;
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

class CashInResource extends Resource
{
    protected static ?string $model = CashIn::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('view_any_cash::in');
    }

    public static function canView($record): bool
    {
        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('view_cash::in');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('create_cash::in');
    }

    public static function canEdit($record): bool
    {
        // Prevent editing if cash in is approved or rejected
        if (\in_array($record->cash_in_status, ['approved', 'rejected'])) {
            return false;
        }

        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('update_cash::in');
    }

    public static function canDelete($record): bool
    {
        // Prevent deleting if cash in is approved or rejected
        if (\in_array($record->cash_in_status, ['approved', 'rejected'])) {
            return false;
        }

        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('delete_cash::in');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->hasRole(['operator', 'super_admin']) || auth()->user()->can('delete_any_cash::in');
    }

    public static function getNavigationLabel(): string
    {
        return __('cash_in.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('cash_in.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('cash_in.title_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('receipt_no')
                            ->label(__('cash_in.fields.receipt_no'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('pks_no')
                            ->label(__('cash_in.fields.pks_no'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->label(__('cash_in.fields.category'))
                            ->options([
                                'internal' => __('cash_in.category.internal'),
                                'external' => __('cash_in.category.external'),
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('cash_in.fields.amount'))
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->step(1),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('cash_in.fields.date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),

                        Forms\Components\TextInput::make('partner_name')
                            ->label(__('cash_in.fields.partner_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('faculty')
                            ->label(__('cash_in.fields.faculty'))
                            ->required()
                            ->maxLength(255),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('cash_in.fields.category'))
                    ->options([
                        'internal' => __('cash_in.category.internal'),
                        'external' => __('cash_in.category.external'),
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(__('cash_in.filters.date_from'))
                            ->native(false),
                        Forms\Components\DatePicker::make('date_until')
                            ->label(__('cash_in.filters.date_until'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListCashIns::route('/'),
            'create' => Pages\CreateCashIn::route('/create'),
            'edit' => Pages\EditCashIn::route('/{record}/edit'),
            'view' => Pages\ViewCashIn::route('/{record}'),
        ];
    }
}
