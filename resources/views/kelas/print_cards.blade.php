<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Absensi</title>
    @vite(['resources/css/app.css', 'resources/css/print.css'])
    
    {{-- Include the same styles used in the preview --}}
    @include('admin.print_cards.partials.card-styles')

    <style>
        /* Additional styles for printing layout */
        body {
            background-color: #f3f4f6; /* gray-100 */
        }
        .page-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }
        .card-wrapper {
            display: flex;
            gap: 10px;
            break-inside: avoid; /* Avoid breaking a card across pages */
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .page-container {
                padding: 0;
                gap: 10px;
                justify-content: flex-start;
            }
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        @forelse ($siswa as $user)
            @php
                // --- CONFIGURATION SETUP ---
                $card_orientation = data_get($config, 'card_orientation', 'portrait');
                $front_config = data_get($config, 'config_json.front', []);
                $back_config = data_get($config, 'config_json.back', []);
                
                $qr_on_back = data_get($config, 'config_json.qr_on_back', false);
                $qrSize = data_get($config, 'config_json.qr_size', 70);
                $qrPosX = data_get($config, 'config_json.qr_position_x', 50);
                $qrPosY = data_get($config, 'config_json.qr_position_y', 50);

                // --- ROLE-SPECIFIC DATA MAPPING ---
                $profile = $user->profile; // Generic profile access
                $userData = [
                    'name' => $user->name,
                    'nis' => $profile->nis ?? $user->identifier,
                    'kelas' => optional($profile->kelas)->nama_kelas ?? ($profile->jabatan ?? $user->custom_role ?? '-'),
                    'tanggal_lahir' => optional($profile)->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d M Y') : '-',
                    'jabatan' => $profile->jabatan ?? ($user->role === 'siswa' ? 'Siswa' : 'Staff'),
                    'nip' => $profile->nip ?? ($user->role !== 'siswa' ? $user->identifier : '-'),
                ];

                $qrCodeUrl = 'data:image/svg+xml;base64,' . base64_encode(QrCode::size($qrSize)->generate($user->identifier));

                $allFields = [
                    ['key' => 'foto', 'label' => 'Foto'],
                    ['key' => 'name', 'label' => 'Nama', 'icon' => 'fa-solid fa-user'],
                    ['key' => 'nis', 'label' => 'NIS/ID Pengguna', 'icon' => 'fa-solid fa-id-badge'],
                    ['key' => 'kelas', 'label' => 'Kelas/Jabatan', 'icon' => 'fa-solid fa-school'],
                    ['key' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'icon' => 'fa-solid fa-cake-candles'],
                    ['key' => 'jabatan', 'label' => 'Jabatan (Staf)', 'icon' => 'fa-solid fa-user-tie'],
                    ['key' => 'nip', 'label' => 'NIP/ID Pegawai', 'icon' => 'fa-solid fa-id-card'],
                ];
                $selected_fields = data_get($front_config, 'selected_fields', []);
            @endphp

            <div class="card-wrapper">
                {{-- FRONT CARD --}}
                <div class="card-preview relative rounded-lg flex flex-col overflow-hidden {{ $card_orientation }}" 
                     style="background-color: {{ data_get($front_config, 'theme.background_color', '#ffffff') }};">

                    @if(data_get($front_config, 'assets.watermark_url'))
                    <div class="absolute inset-0 z-0 flex items-center justify-center"
                         style="justify-content: center; align-items: center; top: {{ data_get($front_config, 'assets.watermark_position_y', 50) - 50 }}%;">
                        <img src="{{ data_get($front_config, 'assets.watermark_url') }}" 
                             alt="Watermark" 
                             class="pointer-events-none"
                             style="opacity: {{ data_get($front_config, 'assets.watermark_opacity', 0.1) }}; width: {{ data_get($front_config, 'assets.watermark_size', 70) }}%;"
                        />
                    </div>
                    @endif

                    <div class="card-header-preview flex-shrink-0 flex items-center z-10 p-2" 
                         style="background-color: {{ data_get($front_config, 'theme.header_background_color', '#1e3a8a') }}; color: {{ data_get($front_config, 'theme.text_color_header', '#ffffff') }}; padding-left: {{ data_get($front_config, 'header_padding_x', 8) }}px; padding-right: {{ data_get($front_config, 'header_padding_x', 8) }}px;">
                        <img src="{{ data_get($front_config, 'assets.logo_url', asset('images/default-avatar.svg')) }}" 
                             alt="Logo Sekolah" class="w-10 h-10 object-contain mr-3">
                        <div class="flex-grow">
                            <p class="font-bold text-sm uppercase leading-tight">{{ data_get($front_config, 'header_title', 'Kartu Absensi') }}</p>
                            <p class="text-xs leading-tight">{{ data_get($front_config, 'school_name', 'Nama Sekolah Anda') }}</p>
                        </div>
                    </div>

                    <div class="card-body-preview flex-grow p-4 flex z-10" 
                         style="color: {{ data_get($front_config, 'theme.text_color_body', '#333333') }};">
                        @if(in_array('foto', $selected_fields))
                        <div class="flex-shrink-0 pr-4">
                             <div class="bg-gray-200 border-2 border-white shadow-md flex items-center justify-center overflow-hidden" 
                                  style="width: {{ data_get($front_config, 'photo_width', 70) }}px; height: {{ data_get($front_config, 'photo_height', 90) }}px;">
                                <img src="{{ $user->foto_url }}" alt="Foto Pengguna" class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif
                        
                        <div class="info flex-grow flex flex-col justify-center text-xs space-y-1">
                             @foreach ($allFields as $field)
                                @if (in_array($field['key'], $selected_fields) && $field['key'] !== 'foto' && !empty($userData[$field['key']]))
                                    <div class="flex items-start">
                                        <i class="{{ $field['icon'] }} w-4 text-center mr-2 mt-0.5 text-gray-500"></i>
                                        <div class="flex-grow">
                                            <div class="text-gray-500 text-[10px] uppercase font-semibold">{{ $field['label'] }}</div>
                                            <div class="font-bold">{{ $userData[$field['key']] }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    @if(!$qr_on_back)
                    <div class="absolute z-20 bg-white p-1 rounded-md shadow"
                         style="width: {{ $qrSize }}px; height: {{ $qrSize }}px; top: calc({{ $qrPosY }}% - {{ $qrSize / 2 }}px); left: calc({{ $qrPosX }}% - {{ $qrSize / 2 }}px);">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-full h-full">
                    </div>
                    @endif
                </div>

                {{-- BACK CARD --}}
                <div class="card-preview rounded-lg flex flex-col relative overflow-hidden {{ $card_orientation }}" 
                     style="background-color: {{ data_get($back_config, 'theme.background_color', '#ffffff') }}; color: {{ data_get($back_config, 'theme.text_color', '#000000') }};">
                    
                    <div class="flex-grow flex flex-col justify-center items-center p-4 text-center">
                        @php
                            $backText = data_get($back_config, 'custom_text', 'Kartu ini adalah milik {nama} dan merupakan properti dari {sekolah}.');
                            $backText = str_replace('{nama}', "<b>{$user->name}</b>", $backText);
                            $backText = str_replace('{sekolah}', "<b>" . data_get($front_config, 'school_name', 'Nama Sekolah Anda') . "</b>", $backText);
                        @endphp
                        <div class="text-xs leading-relaxed">{!! $backText !!}</div>
                    </div>
                    
                    @if($qr_on_back)
                    <div class="absolute z-20"
                         style="width: {{ $qrSize }}px; height: {{ $qrSize }}px; top: calc({{ $qrPosY }}% - {{ $qrSize / 2 }}px); left: calc({{ $qrPosX }}% - {{ $qrSize / 2 }}px);">
                        <div class="w-full h-full bg-white p-1 rounded-md shadow-lg flex items-center justify-center">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-full h-full">
                        </div>
                    </div>
                    @endif

                    <div class="flex-shrink-0 text-center text-xs p-2 border-t" style="border-color: rgba(0, 0, 0, 0.1);">
                        <p>{{ data_get($front_config, 'school_name', 'Nama Sekolah Anda') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-full">Tidak ada pengguna untuk ditampilkan.</p>
        @endforelse
    </div>
</body>
</html>
