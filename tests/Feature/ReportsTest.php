<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Report;
use App\Livewire\Reports;
use Illuminate\Support\Str;
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
    livewire(Reports::class)
        ->set('search', 'Test')
        ->assertHasNoErrors();

    expect(Str::contains(URL::current(), '?page'))
        ->toBeFalse();
});

it('can delete a report', function () {
    livewire(Reports::class)
        ->call('delete', Report::first()->id)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(Reports::class)
        ->assertHasNoErrors();
});
