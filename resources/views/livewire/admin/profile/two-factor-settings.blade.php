<div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="font-semibold text-gray-900 mb-1">Two-Factor Authentication</h3>
    <p class="text-sm text-gray-600 mb-4">Tambah lapisan keamanan menggunakan aplikasi authenticator seperti Google Authenticator atau Authy.</p>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    @if ($stage === 'idle')
        <button wire:click="beginSetup" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Aktifkan 2FA
        </button>
    @endif

    @if ($stage === 'setup')
        <div class="space-y-4">
            <p class="text-sm text-gray-700">Scan QR code di bawah, lalu masukkan kode 6 digit untuk konfirmasi.</p>

            <div class="flex items-start gap-6">
                <div>
                    <div id="tfa-qr" data-uri="{{ $this->otpauthUri }}" class="bg-white p-2 border rounded"></div>
                    <p class="text-xs text-gray-500 mt-2 break-all">Secret: <code>{{ $secret }}</code></p>
                </div>

                <div class="flex-1 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode dari aplikasi authenticator</label>
                        <input type="text" wire:model="confirmCode" inputmode="numeric" maxlength="6"
                               class="mt-1 w-40 rounded border-gray-300" placeholder="123456">
                        @error('confirmCode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button wire:click="confirm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Konfirmasi & Aktifkan
                    </button>
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-sm font-medium text-gray-700">Recovery codes (simpan baik-baik):</p>
                <div class="grid grid-cols-2 gap-1 mt-2 font-mono text-sm">
                    @foreach ($recoveryCodes as $rc)
                        <div class="bg-gray-100 px-2 py-1 rounded">{{ $rc }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
            <script>
                (function () {
                    const el = document.getElementById('tfa-qr');
                    if (!el) return;
                    new QRious({ element: document.createElement('canvas'), value: el.dataset.uri, size: 200 })
                        ._element && el.appendChild(document.querySelector('canvas') || (() => {
                            const c = document.createElement('canvas');
                            new QRious({ element: c, value: el.dataset.uri, size: 200 });
                            return c;
                        })());
                })();
            </script>
        @endpush
    @endif

    @if ($stage === 'confirmed')
        <div class="p-3 bg-green-50 border border-green-200 rounded mb-4">
            <p class="text-sm text-green-800">2FA aktif untuk akun Anda.</p>
        </div>

        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Untuk menonaktifkan, masukkan password Anda:</label>
            <input type="password" wire:model="currentPassword" class="w-64 rounded border-gray-300">
            @error('currentPassword') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <div>
                <button wire:click="disable" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Nonaktifkan 2FA
                </button>
            </div>
        </div>
    @endif
</div>
