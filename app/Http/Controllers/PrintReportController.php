<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use Spatie\LaravelPdf\PdfBuilder;
use function Spatie\LaravelPdf\Support\pdf;

class PrintReportController
{
    public function __invoke(Report $report): PdfBuilder
    {
        return pdf()->view('print-report', [
            'report' => $report,
            'report_transactions' => $report->transactions()
                ->with(['account', 'category.parent'])
                ->latest('date')
                ->get()
        ])->name("{$report->slug}.pdf");
    }
}
