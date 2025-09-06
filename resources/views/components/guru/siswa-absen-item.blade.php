@props(['siswa'])

<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
    <div class="flex items-center">
        <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ $siswa->foto_url }}" alt="{{ $siswa->name ?? 'Siswa' }}">
        <div>
            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $siswa->name ?? 'Siswa tidak ditemukan' }}</p>
            @if($siswa->siswaProfile?->nis)
                <p class="text-xs text-gray-500 dark:text-gray-400">NIS: {{ $siswa->siswaProfile->nis }}</p>
            @else
                <p class="text-xs text-red-500 dark:text-red-400">Data tidak lengkap</p>
            @endif
        </div>
    </div>
    
</div>
