<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Report;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->has(Report::factory())
            ->create()
    );

    Pdf::fake();
});

it('can generate a pdf of a report', function () {
    $report = Report::first();

    $this->get(route('print-report', $report))->assertOk();

    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) use ($report): bool {
        return $pdf->downloadName === "{$report->slug}.pdf";
    });
});
