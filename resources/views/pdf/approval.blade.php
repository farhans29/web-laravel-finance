<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h2 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-item strong {
            display: inline-block;
            width: 150px;
            color: #555;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-not-approved {
            background-color: #fff3cd;
            color: #856404;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature-section {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .signature-box {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .signature-box p {
            margin: 10px 0;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 40px auto 10px;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE APPROVAL DOCUMENT</h1>
        <p><strong>Generated:</strong> {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="info-section">
        <h2>Invoice Information</h2>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <strong>Invoice Number:</strong> {{ $invoice->invoice_no }}
                </div>
                <div class="info-item">
                    <strong>Name:</strong> {{ $invoice->name }}
                </div>
                <div class="info-item">
                    <strong>Partner:</strong> {{ $invoice->partner }}
                </div>
                <div class="info-item">
                    <strong>Activity:</strong> {{ $invoice->activity_name }}
                </div>
            </div>
            <div>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-{{ $invoice->invoice_status }}">
                        {{ $invoice->invoice_status == 'approved' ? 'APPROVED' : 'NOT APPROVED' }}
                    </span>
                </div>
                <div class="info-item">
                    <strong>Virtual Account:</strong> {{ $invoice->virtual_account_no }}
                </div>
                <div class="info-item">
                    <strong>Created:</strong> {{ $invoice->created_at->format('d F Y H:i') }}
                </div>
                <div class="info-item">
                    <strong>Updated:</strong> {{ $invoice->updated_at->format('d F Y H:i') }}
                </div>
            </div>
        </div>
        <div class="info-item">
            <strong>Total Amount:</strong> 
            <span class="amount">Rp {{ number_format($invoice->bill, 2) }}</span>
        </div>
    </div>

    {{-- <div class="info-section">
        <h2>Additional Information</h2>
        <div class="info-item">
            <strong>Invoice ID:</strong> #{{ $invoice->id }}
        </div>
        <div class="info-item">
            <strong>Status History:</strong>
            This invoice has been {{ $invoice->invoice_status == 'approved' ? 'approved for payment processing' : 'marked as awaiting approval' }}.
        </div>
    </div> --}}

    {{-- <div class="signature-section">
        <div class="signature-box">
            <h3>Supervisor</h3>
            <div class="signature-line"></div>
            <p>{{ auth()->user()->name }}</p>
            <p>{{ now()->format('d F Y') }}</p>
        </div>
        <div class="signature-box">
            <h3>Approval Status</h3>
            <p><strong>{{ $invoice->invoice_status == 'approved' ? '✓ APPROVED' : '⏳ PENDING' }}</strong></p>
            <p>{{ $invoice->invoice_status == 'approved' ? 'This invoice is approved for payment processing.' : 'This invoice is awaiting approval.' }}</p>
        </div>
    </div> --}}

    <div class="footer">
        <p>This document was generated automatically from the Invoice Approval System.</p>
        <p>For any questions regarding this approval, please contact the system administrator.</p>
    </div>
</body>
</html>