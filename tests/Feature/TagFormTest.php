<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use App\Livewire\TagForm;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $user = User::factory()->create();

    Tag::factory()->for($user)->count(5)->create();

    $this->actingAs($user);
});

it('can create a tag', function () {
    livewire(TagForm::class)
        ->set('name', 'Test tag')
        ->call('submit')
        ->assertDispatched('tag-saved')
        ->assertHasNoErrors();
});

it('can edit a tag', function () {
    livewire(TagForm::class, ['tag' => auth()->user()->tags->first()])
        ->set('name', 'Test tag updated')
        ->call('submit')
        ->assertDispatched('tag-saved')
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(TagForm::class)
        ->assertHasNoErrors();
});
