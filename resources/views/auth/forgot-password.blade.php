<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — {{ setting('site_name', config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-2">Lupa Password</h1>
        <p class="text-gray-600 text-sm mb-6">Masukkan email Anda. Kami akan mengirim link reset password.</p>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded border-gray-300">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Kirim Link Reset
            </button>
        </form>

        <p class="mt-6 text-sm text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">← Kembali ke login</a>
        </p>
    </div>
</body>
</html>
