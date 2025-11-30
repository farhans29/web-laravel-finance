<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApproval extends ViewRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => !\in_array($this->getRecord()->invoice_status, ['approved', 'rejected'])),
            Actions\Action::make('download_pdf')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    $invoice = $this->getRecord();
                    return response()->streamDownload(function () use ($invoice) {
                        $Pdf = app('dompdf.wrapper');
                        echo $Pdf->loadView('pdf.approval', ['invoice' => $invoice])->output();
                    }, 'invoice_' . $invoice->invoice_no . '_approval.pdf', [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
        ];
    }
}
