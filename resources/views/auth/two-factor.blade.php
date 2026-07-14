<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA — {{ setting('site_name', config('app.name')) }}</title>
    @if(setting('site_favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-2">Verifikasi Two-Factor</h1>
        <p class="text-sm text-gray-600 mb-6">Masukkan kode 6 digit dari aplikasi authenticator Anda, atau gunakan recovery code.</p>

        <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode</label>
                <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" autofocus
                       class="mt-1 w-full rounded border-gray-300 font-mono text-lg tracking-wider"
                       placeholder="123456 or recovery code">
                @error('code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Verifikasi
            </button>
        </form>

        <p class="mt-6 text-sm text-center">
            <a href="{{ route('login') }}" class="text-gray-500 hover:underline">Batal</a>
        </p>
    </div>
</body>
</html>
