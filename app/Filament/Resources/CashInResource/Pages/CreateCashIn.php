<?php

namespace App\Filament\Resources\CashInResource\Pages;

use App\Filament\Resources\CashInResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCashIn extends CreateRecord
{
    protected static string $resource = CashInResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
