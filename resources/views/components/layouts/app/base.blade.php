<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
	
    <body class="min-h-screen bg-white dark:bg-zinc-800 max-w-3xl mx-auto px-8 py-10 space-y-4">
		<div class="flex justify-center space-x-2 items-center gap-2 font-medium">
			<div class="flex items-center justify-center rounded-md">
				<img src="{{ asset('icon.svg') }}" class="w-30" />
			</div>

			<h1 class="text-2xl">
				{{ config('app.name', 'Pure Finance') }}
			</h1>
		</div>

        {{ $slot }}
    </body>
</html>
