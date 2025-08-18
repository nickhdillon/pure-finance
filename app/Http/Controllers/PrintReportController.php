<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintReportController
{
    public function __invoke(Report $report): Response
    {
        return Pdf::loadView('print-report', [
            'report' => $report,
            'report_transactions' => $report->transactions()
                ->with(['account', 'category.parent'])
                ->latest('date')
                ->get()
        ])->stream();
    }
}
