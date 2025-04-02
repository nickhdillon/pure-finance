<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class CreateDefaultCategories
{
	public Collection $parent_categories;

	public Collection $child_categories;

	public function __construct()
	{
		$this->parent_categories = collect([
			'Auto & Transport',
			'Food',
			'Home',
			'Health',
			'Personal Care',
			'Personal Income',
			'Pets',
			'Shopping',
			'Travel',
			'Utilities',
		]);

		$this->child_categories = collect([
			'Car Insurance',
			'Car Payment',
			'Fast Food',
			'Restaurants',
			'Mortgage',
			'Rent',
			'Doctor',
			'Pharmacy',
			'Haircut',
			'Laundry',
			'Paycheck',
			'Bonus',
			'Pet Food',
			'Veterinary',
			'Clothing',
			'Gifts',
			'Hotel',
			'Airfare',
			'Gas',
			'Electric',
		]);
	}

	public function handle(User $user): void
	{
		defer(function () use ($user): void {
			$parent_categories = $this->createParentCategories($user);

			$this->createChildCategories($parent_categories, $user);
		});
	}

	private function createParentCategories(User $user): Collection
	{
		return $this->parent_categories->map(function (string $parent) use ($user): Model {
			return $user->categories()->create(['name' => $parent]);
		});
	}

	private function createChildCategories(Collection $parent_categories, User $user): void
	{
		$parent_index = 0;

		$this->child_categories->each(
			function (string $child, int $index)
			use ($parent_categories, &$parent_index, $user): void {
				$parent = $parent_categories->get($parent_index);

				$user->categories()->create([
					'name' => $child,
					'parent_id' => $parent->id
				]);

				if (($index + 1) % 2 === 0) {
					$parent_index++;
				}
			}
		);
	}
}
