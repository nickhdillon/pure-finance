<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Report;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->has(Report::factory())
            ->create()
    );
});

it('can generate a pdf of a report', function () {
    $report = Report::first();

    $this->get(route('print-report', $report))->assertOk();
});
