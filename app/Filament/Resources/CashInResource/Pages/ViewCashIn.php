<?php

namespace App\Filament\Resources\CashInResource\Pages;

use App\Filament\Resources\CashInResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashIn extends ViewRecord
{
    protected static string $resource = CashInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
