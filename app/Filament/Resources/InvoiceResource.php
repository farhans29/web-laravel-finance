<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_invoice') || auth()->user()->hasRole('super_admin');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoice.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('invoice.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('invoice.title_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('invoice_no')
                            ->label(__('invoice.fields.invoice_no'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name')
                            ->label(__('invoice.fields.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('partner')
                            ->label(__('invoice.fields.partner'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('activity_name')
                            ->label(__('invoice.fields.activity_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('virtual_account_no')
                            ->label(__('invoice.fields.virtual_account_no'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bill')
                            ->label(__('invoice.fields.bill'))
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->step(1),

                        Forms\Components\Select::make('invoice_status')
                            ->label(__('invoice.fields.invoice_status'))
                            ->options([
                                'approved' => __('invoice.status.approved'),
                                'not_approved' => __('invoice.status.not_approved'),
                            ])
                            ->default('not_approved')
                            ->required()
                            ->hiddenOn('create')
                            ->disabled(fn () => !auth()->user()->hasRole('supervisor')),
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

                Tables\Columns\TextColumn::make('virtual_account_no')
                    ->label(__('invoice.fields.virtual_account_no'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('bill')
                    ->label(__('invoice.fields.bill'))
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('invoice_status')
                    ->label(__('invoice.fields.invoice_status'))
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'not_approved',
                    ])
                    ->formatStateUsing(fn (string $state): string => __('invoice.status.' . $state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('invoice.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invoice_status')
                    ->label(__('invoice.fields.invoice_status'))
                    ->options([
                        'approved' => __('invoice.status.approved'),
                        'not_approved' => __('invoice.status.not_approved'),
                    ]),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
