<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Absensi Siswa</title>
    @vite(['resources/css/app.css'])
    
    <style>
        /* Define fixed card dimensions for consistent printing */
        :root {
            --card-portrait-width: 204.0px;  /* 54mm at 96dpi */
            --card-portrait-height: 323.5px; /* 85.5mm at 96dpi */
            --card-landscape-width: 323.5px;
            --card-landscape-height: 204.0px;
        }

        /* Default card dimensions for screen (can be overridden by orientation) */
        .card {
            width: var(--card-landscape-width);
            height: var(--card-landscape-height);
        }

        /* Portrait orientation for screen */
        .card.portrait {
            width: var(--card-portrait-width);
            height: var(--card-portrait-height);
        }

        /* Landscape orientation for screen */
        .card.landscape {
            width: var(--card-landscape-width);
            height: var(--card-landscape-height);
        }

        @media print {
            /* For printing, ensure exact dimensions and no scaling issues */
            .card {
                width: var(--card-landscape-width) !important;
                height: var(--card-landscape-height) !important;
                page-break-inside: avoid;
            }

            .card.portrait {
                width: var(--card-portrait-width) !important;
                height: var(--card-portrait-height) !important;
            }

            .card.landscape {
                width: var(--card-landscape-width) !important;
                height: var(--card-landscape-height) !important;
            }

            @page {
                size: A4;
                margin: 10mm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    @php
        // --- THEME AND ASSET CONFIGURATION ---
        $theme = data_get($config, 'config_json.theme', []);
        $assets = data_get($config, 'config_json.assets', []);

        // Helper function for RGBA conversion
        if (!function_exists('hexToRgba')) {
            function hexToRgba($hex, $alpha = 1) {
                $hex = ltrim($hex, '#');
                if (strlen($hex) == 3) {
                    $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                }
                $rgb = sscanf($hex, "%02x%02x%02x");
                $r = $rgb[0] ?? 255; $g = $rgb[1] ?? 255; $b = $rgb[2] ?? 255;
                return "rgba($r, $g, $b, $alpha)";
            }
        }

        // Card Background
        $rgbaBgColor = hexToRgba($theme['background_color'] ?? '#ffffff', $theme['background_opacity'] ?? 1);

        // Header Background
        $rgbaHeaderBgColor = hexToRgba($theme['header_background_color'] ?? '#1e3a8a', $theme['header_background_opacity'] ?? 1);

        // Text Colors
        $header_text_color = $theme['text_color_header'] ?? '#ffffff';
        $body_text_color = $theme['text_color_body'] ?? '#333333';

        // Assets
        $logoUrl = $assets['logo_url'] ?? null;
        $watermarkUrl = $assets['watermark_url'] ?? null;
        $watermarkOpacity = $assets['watermark_opacity'] ?? 0.1;
        $watermarkEnabled = data_get($config, 'config_json.watermark_enabled', true);
        $watermarkSize = data_get($config, 'config_json.assets.watermark_size', 70);
        $watermarkPositionY = data_get($config, 'config_json.assets.watermark_position_y', 50);

        // Photo Size
        $photoWidth = data_get($config, 'config_json.photo_width', 70);
        $photoHeight = data_get($config, 'config_json.photo_height', 90);

        // Header Padding
        $headerPaddingX = data_get($config, 'config_json.header_padding_x', 8);

        // QR Code Position
        $qrPositionX = data_get($config, 'config_json.qr_position_x', 75);
        $qrPositionY = data_get($config, 'config_json.qr_position_y', 75);

        // Card Orientation
        $cardOrientation = $config->card_orientation ?? 'portrait'; // Default to portrait
    @endphp

    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, var(--card-portrait-width));
            gap: 10px; /* Jarak antar kartu */
            justify-content: center;
            padding: 5px;
        }

        /* Override for landscape orientation */
        @if ($cardOrientation === 'landscape')
            .card-container {
                grid-template-columns: repeat(auto-fit, var(--card-landscape-width));
            }
        @endif
        .card {
            position: relative;
            overflow: hidden;
            break-inside: avoid;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            background-color: {{ $rgbaBgColor }};
        }
        .card.portrait {
            width: var(--card-portrait-width);
            height: var(--card-portrait-height);
        }
        .card.landscape {
            width: var(--card-landscape-width);
            height: var(--card-landscape-height);
        }

        .card-header { padding: 5px 8px; display: flex; align-items: center; position: relative; z-index: 1; background-color: {{ $rgbaHeaderBgColor }}; color: {{ $header_text_color }}; }
        .card-header img { width: 35px; height: 35px; margin-right: 8px; }
        .card-header p { font-size: 10px; margin: 0; }
        .card-header .title { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .card-body { flex-grow: 1; display: flex; padding: 8px; position: relative; z-index: 1; color: {{ $body_text_color }}; }
        .card-body .photo { flex-shrink: 0; margin-right: 8px; }
        .card-body .photo img { object-fit: cover; border: 1px solid white; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-body .info { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .card-body .info table { font-size: 9px; width: 100%; color: inherit; }
        .card-body .info table td { padding-bottom: 1px; }
        
        /* QR section positioning */
        .qr-section {
            position: absolute;
            left: {{ $qrPositionX }}%;
            top: {{ $qrPositionY }}%;
            transform: translate(-50%, -50%);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .qr-code-container { border: 2px solid white; border-radius: 6px; box-shadow: 0 3px 5px rgba(0,0,0,0.1); padding: 2px; background-color: white; display: flex; justify-content: center; align-items: center; }
        .card-watermark { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-repeat: no-repeat; z-index: 0; }

        /* Print-specific styles */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .card-container { justify-content: flex-start; }
            /* Card dimensions are now controlled by :root variables and .card classes */

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        @forelse ($siswa as $s)
            <div class="card rounded-lg {{ $cardOrientation }}">
                @if ($watermarkUrl && data_get($config, 'config_json.watermark_enabled', true))
                <div class="card-watermark" style="background-image: url('{{ $watermarkUrl }}'); opacity: {{ $watermarkOpacity }}; background-size: {{ $watermarkSize }}%; background-position: center {{ $watermarkPositionY }}%;"></div>
                @endif
                <!-- Header -->
                <div class="card-header" style="padding-left: {{ $headerPaddingX }}px; padding-right: {{ $headerPaddingX }}px;">
                    @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo Sekolah">
                    @else
                    <div style="width: 35px; height: 35px; margin-right: 8px;"></div>
                    @endif
                    <div>
                        <p class="title">{{ data_get($config, 'config_json.header_title', 'Kartu Absensi Siswa') }}</p>
                        <p style="font-size: 10px;">{{ data_get($config, 'config_json.school_name', setting('nama_sekolah', 'Nama Sekolah')) }}</p>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body">
                    <!-- Foto -->
                    @if (in_array('foto', data_get($config, 'config_json.selected_fields', [])))
                        <div class="photo">
                            <img src="{{ $s->foto_url }}" alt="Foto Siswa" style="width: {{ $photoWidth }}px; height: {{ $photoHeight }}px;">
                        </div>
                    @endif
                    <!-- Info & QR -->
                    <div class="info">
                        <table>
                            <tbody>
                                @if (in_array('name', data_get($config, 'config_json.selected_fields', [])))
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Nama</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top font-bold">{{ strtoupper($s->name) }}</td>
                                    </tr>
                                @endif
                                @if (in_array('nis', data_get($config, 'config_json.selected_fields', [])))
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">NIS</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top">{{ $s->identifier }}</td>
                                    </tr>
                                @endif
                                @if (in_array('kelas', data_get($config, 'config_json.selected_fields', [])))
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Kelas</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top">{{ $s->siswaProfile->kelas->nama_kelas ?? '-' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="font-semibold pr-1 align-top">Masa Berlaku</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">Selama menjadi siswa</td>
                                </tr>
                                @if (in_array('tanggal_lahir', data_get($config, 'config_json.selected_fields', [])))
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Tempat/Tgl Lahir</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top">{{ $s->siswaProfile->tempat_lahir ?? '-' }}, {{ $s->siswaProfile->tanggal_lahir ? \Carbon\Carbon::parse($s->siswaProfile->tanggal_lahir)->format('d M Y') : '-' }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="qr-section">
                            <div class="qr-code-container">
                                {!! QrCode::size(data_get($config, 'config_json.qr_size', 70))->generate($s->identifier) !!}
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-full">Tidak ada siswa untuk ditampilkan.</p>
        @endforelse
    </div>
</body>
</html>
