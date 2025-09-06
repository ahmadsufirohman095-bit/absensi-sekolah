<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR Code Absensi - {{ setting('nama_sekolah', 'Sistem Absensi') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact; /* Untuk memastikan background dan warna tercetak di Chrome */
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8 max-w-lg bg-white shadow-lg rounded-lg">
        <div class="text-center">
            <!-- Header Halaman Cetak -->
            <div class="flex flex-col items-center mb-6">
                <img class="w-24 h-24 mb-4" src="{{ asset('images/icon_mts_al_muttaqin.png') }}" alt="Logo Sekolah">
                <h1 class="text-2xl font-bold text-gray-800">{{ setting('nama_sekolah', 'Sistem Absensi') }}</h1>
                <p class="text-gray-500">{{ setting('alamat_sekolah', 'Alamat Sekolah') }}</p>
            </div>

            <div class="border-t border-b border-gray-200 py-6">
                <h2 class="text-xl font-semibold text-gray-700">ABSENSI MASUK</h2>
                <p class="text-gray-600 mt-2">Silakan pindai (scan) QR Code di bawah ini untuk mencatatkan kehadiran Anda.</p>
                <p class="font-bold text-gray-800 text-lg mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center my-8">
                <div class="p-4 bg-white border rounded-lg">
                    {!! QrCode::size(300)->generate($scanUrl) !!}
                </div>
            </div>

            <p class="text-sm text-red-500 font-semibold">QR Code ini bersifat rahasia dan berlaku untuk satu sesi absensi.</p>
        </div>

         <!-- Tombol Aksi -->
        <div class="mt-10 text-center no-print">
            <button onclick="window.print()" class="bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cetak Halaman Ini
            </button>
             <a href="{{ route('dashboard') }}" class="ml-4 text-gray-600 hover:text-gray-800">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
