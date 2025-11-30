<?php

namespace App\Filament\Resources\CashInApprovalResource\Pages;

use App\Filament\Resources\CashInApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashInApprovals extends ListRecords
{
    protected static string $resource = CashInApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
