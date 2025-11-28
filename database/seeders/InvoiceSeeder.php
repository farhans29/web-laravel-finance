<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = [
            [
                'invoice_no' => 'INV-2025-001',
                'name' => 'Website Development',
                'partner' => 'Tech Solutions Inc',
                'activity_name' => 'Web Development Services',
                'virtual_account_no' => '8001234567890',
                'bill' => 5000.00,
                'invoice_status' => 'approved',
            ],
            [
                'invoice_no' => 'INV-2025-002',
                'name' => 'Digital Marketing',
                'partner' => 'Marketing Pro Ltd',
                'activity_name' => 'Social Media Campaign',
                'virtual_account_no' => '8009876543210',
                'bill' => 2500.00,
                'invoice_status' => 'not_approved',
            ],
            [
                'invoice_no' => 'INV-2025-003',
                'name' => 'Cloud Infrastructure',
                'partner' => 'Cloud Services Corp',
                'activity_name' => 'AWS Setup and Configuration',
                'virtual_account_no' => '8005555555555',
                'bill' => 7500.00,
                'invoice_status' => 'approved',
            ],
            [
                'invoice_no' => 'INV-2025-004',
                'name' => 'Mobile App Development',
                'partner' => 'AppDev Studios',
                'activity_name' => 'iOS and Android App',
                'virtual_account_no' => '8001111111111',
                'bill' => 15000.00,
                'invoice_status' => 'not_approved',
            ],
            [
                'invoice_no' => 'INV-2025-005',
                'name' => 'SEO Optimization',
                'partner' => 'SEO Masters',
                'activity_name' => 'Website SEO Audit and Improvements',
                'virtual_account_no' => '8002222222222',
                'bill' => 1200.00,
                'invoice_status' => 'approved',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
}
