<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash In Approval</title>
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
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h2 {
            color: #28a745;
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
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .category-internal {
            background-color: #cfe2ff;
            color: #084298;
        }
        .category-external {
            background-color: #d1e7dd;
            color: #0f5132;
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
        <h1>CASH IN APPROVAL DOCUMENT</h1>
        <p><strong>Generated:</strong> {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="info-section">
        <h2>Cash In Information</h2>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <strong>Receipt Number:</strong> {{ $cashIn->receipt_no }}
                </div>
                <div class="info-item">
                    <strong>PKS Number:</strong> {{ $cashIn->pks_no }}
                </div>
                <div class="info-item">
                    <strong>Partner Name:</strong> {{ $cashIn->partner_name }}
                </div>
                <div class="info-item">
                    <strong>Faculty:</strong> {{ $cashIn->faculty }}
                </div>
            </div>
            <div>
                <div class="info-item">
                    <strong>Category:</strong>
                    <span class="category-badge category-{{ $cashIn->category }}">
                        {{ strtoupper($cashIn->category) }}
                    </span>
                </div>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-{{ $cashIn->cash_in_status }}">
                        {{ $cashIn->cash_in_status == 'approved' ? 'APPROVED' : ($cashIn->cash_in_status == 'rejected' ? 'REJECTED' : 'NOT APPROVED') }}
                    </span>
                </div>
                <div class="info-item">
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($cashIn->date)->format('d F Y') }}
                </div>
                <div class="info-item">
                    <strong>Created:</strong> {{ $cashIn->created_at->format('d F Y H:i') }}
                </div>
            </div>
        </div>
        <div class="info-item">
            <strong>Total Amount:</strong>
            <span class="amount">Rp {{ number_format($cashIn->amount, 2) }}</span>
        </div>
    </div>

    <div class="info-section">
        <h2>Tracking Information</h2>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <strong>Created By:</strong> {{ $cashIn->creator?->name ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <strong>Created At:</strong> {{ $cashIn->created_at->timezone('Asia/Jakarta')->format('d F Y H:i') }}
                </div>
                <div class="info-item">
                    <strong>Updated By:</strong> {{ $cashIn->updater?->name ?? 'N/A' }}
                </div>
            </div>
            <div>
                <div class="info-item">
                    <strong>Updated At:</strong> {{ $cashIn->updated_at->timezone('Asia/Jakarta')->format('d F Y H:i') }}
                </div>
                <div class="info-item">
                    <strong>Approved By:</strong> {{ $cashIn->approver?->name ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <strong>Approved At:</strong> {{ $cashIn->approved_at?->timezone('Asia/Jakarta')->format('d F Y H:i') ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document was generated automatically from the Cash In Approval System.</p>
        <p>For any questions regarding this approval, please contact the system administrator.</p>
    </div>
</body>
</html>
