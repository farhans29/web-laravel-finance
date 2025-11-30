<?php

namespace App\Filament\Resources\CashInApprovalResource\Pages;

use App\Filament\Resources\CashInApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewCashInApproval extends ViewRecord
{
    protected static string $resource = CashInApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    $cashIn = $this->getRecord();
                    return response()->streamDownload(function () use ($cashIn) {
                        $Pdf = app('dompdf.wrapper');
                        echo $Pdf->loadView('pdf.cash-in-approval', ['cashIn' => $cashIn])->output();
                    }, 'cash_in_' . $cashIn->receipt_no . '_approval.pdf', [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
        ];
    }
}
