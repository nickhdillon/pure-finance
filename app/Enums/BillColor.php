<?php

declare(strict_types=1);

namespace App\Enums;

enum BillColor: string
{
	case BLUE = 'blue';
	case GREEN = 'green';
	case ORANGE = 'orange';
	case RED = 'red';
	case YELLOW = 'yellow';

	public function label(): string
	{
		return match ($this) {
			self::BLUE => 'Blue',
			self::GREEN => 'Green',
			self::ORANGE => 'Orange',
			self::RED => 'Red',
			self::YELLOW => 'Yellow',
		};
	}

	public function labelColor(): string
	{
		return match ($this) {
			self::BLUE => 'bg-blue-500',
			self::GREEN => 'bg-emerald-500',
			self::ORANGE => 'bg-orange-500',
			self::RED => 'bg-red-500',
			self::YELLOW => 'bg-yellow-400',
		};
	}

	public function bgColor(): string
	{
		return match ($this) {
			self::BLUE => 'bg-blue-400/20 dark:bg-blue-400/40',
			self::GREEN => 'bg-emerald-400/20 dark:bg-emerald-400/40',
			self::ORANGE => 'bg-orange-400/20 dark:bg-orange-400/40',
			self::RED => 'bg-red-400/20 dark:bg-red-400/40',
			self::YELLOW => 'bg-yellow-400/25 dark:bg-yellow-400/40',
		};
	}

	public function textColor(): string
	{
		return match ($this) {
			self::BLUE => 'text-blue-800 dark:text-blue-200',
			self::GREEN => 'text-emerald-800 dark:text-emerald-200',
			self::ORANGE => 'text-orange-700 dark:text-orange-200',
			self::RED => 'text-red-700 dark:text-red-200',
			self::YELLOW => 'text-yellow-800 dark:text-yellow-200',
		};
	}
}
