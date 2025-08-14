<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Report;
use App\Livewire\Reports;
use Illuminate\Support\Str;
use App\Livewire\ReportView;
use Illuminate\Support\Facades\URL;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(
        User::factory()
            ->has(Report::factory())
            ->create()
    );
});

it('can update search', function () {
    livewire(ReportView::class, ['report' => Report::first()])
        ->set('search', 'Test')
        ->assertHasNoErrors();

    expect(Str::contains(URL::current(), '?page'))
        ->toBeFalse();
});

it('can update report name', function () {
    livewire(ReportView::class, ['report' => Report::first()])
        ->call('submit')
        ->assertHasNoErrors();
});

it('can delete report', function () {
    livewire(ReportView::class, ['report' => Report::first()])
        ->call('delete')
        ->assertRedirect(Reports::class)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(ReportView::class, ['report' => Report::first()])
        ->assertHasNoErrors();
});
