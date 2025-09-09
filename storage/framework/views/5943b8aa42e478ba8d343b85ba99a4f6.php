<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Absensi Siswa</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    
    

    <?php
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

    ?>

    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: #f0f0f0; }
        .card-container { display: grid; grid-template-columns: repeat(auto-fit, 323.5px); gap: 10px; justify-content: center; padding: 5px; }
        .card { height: 204.0px; position: relative; overflow: hidden; break-inside: avoid; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; flex-direction: column; background-color: <?php echo e($rgbaBgColor); ?>; }
        .card-header { padding: 5px 8px; display: flex; align-items: center; position: relative; z-index: 1; background-color: <?php echo e($rgbaHeaderBgColor); ?>; color: <?php echo e($header_text_color); ?>; }
        .card-header img { width: 35px; height: 35px; margin-right: 8px; }
        .card-header p { font-size: 10px; margin: 0; }
        .card-header .title { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .card-body { flex-grow: 1; display: flex; padding: 8px; position: relative; z-index: 1; color: <?php echo e($body_text_color); ?>; }
        .card-body .photo { flex-shrink: 0; margin-right: 8px; }
        .card-body .photo img { object-fit: cover; border: 1px solid white; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-body .info { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .card-body .info table { font-size: 9px; width: 100%; color: inherit; }
        .card-body .info table td { padding-bottom: 1px; }
        .card-body .info .qr-section { display: flex; justify-content: flex-end; align-self: flex-end; margin-top: 3px; }
        
        .qr-code-container { border: 2px solid white; border-radius: 6px; box-shadow: 0 3px 5px rgba(0,0,0,0.1); padding: 2px; background-color: white; display: flex; justify-content: center; align-items: center; }
        .card-watermark { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-repeat: no-repeat; z-index: 0; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .card-container { justify-content: flex-start; }
            .card { page-break-inside: avoid; }

            @page {
                size: A4;
                margin: 10mm;
            }

            @media (orientation: landscape) {
                .card-container { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; }
                .card { width: 100%; height: auto; aspect-ratio: 323.5 / 204; }
            }

            @media (orientation: portrait) {
                .card-container { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; }
                .card { width: 100%; height: auto; aspect-ratio: 323.5 / 204; }
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card rounded-lg">
                                <?php if($watermarkUrl): ?>
                <div class="card-watermark" style="background-image: url('<?php echo e($watermarkUrl); ?>'); opacity: <?php echo e($watermarkEnabled ? $watermarkOpacity : 0); ?>; background-size: <?php echo e($watermarkSize); ?>%; background-position: center <?php echo e($watermarkPositionY); ?>%;"></div>
                <?php endif; ?>
                <!-- Header -->
                <div class="card-header" style="padding-left: <?php echo e($headerPaddingX); ?>px; padding-right: <?php echo e($headerPaddingX); ?>px;">
                                        <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="Logo Sekolah">
                    <?php else: ?>
                    <div style="width: 35px; height: 35px; margin-right: 8px;"></div>
                    <?php endif; ?>
                    <div>
                        <p class="title"><?php echo e(data_get($config, 'config_json.header_title', 'Kartu Absensi Siswa')); ?></p>
                        <p style="font-size: 10px;"><?php echo e(data_get($config, 'config_json.school_name', setting('nama_sekolah', 'Nama Sekolah'))); ?></p>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body">
                    <!-- Foto -->
                    <?php if(in_array('foto', data_get($config, 'config_json.selected_fields', []))): ?>
                        <div class="photo">
                            <img src="<?php echo e($s->foto_url); ?>" alt="Foto Siswa" style="width: <?php echo e($photoWidth); ?>px; height: <?php echo e($photoHeight); ?>px;">
                        </div>
                    <?php endif; ?>
                    <!-- Info & QR -->
                    <div class="info">
                        <table>
                            <tbody>
                                <?php if(in_array('name', data_get($config, 'config_json.selected_fields', []))): ?>
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Nama</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top font-bold"><?php echo e(strtoupper($s->name)); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(in_array('nis', data_get($config, 'config_json.selected_fields', []))): ?>
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">NIS</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top"><?php echo e($s->identifier); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(in_array('kelas', data_get($config, 'config_json.selected_fields', []))): ?>
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Kelas</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top"><?php echo e($s->siswaProfile->kelas->nama_kelas ?? '-'); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="font-semibold pr-1 align-top">Masa Berlaku</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">Selama menjadi siswa</td>
                                </tr>
                                <?php if(in_array('tanggal_lahir', data_get($config, 'config_json.selected_fields', []))): ?>
                                    <tr>
                                        <td class="font-semibold pr-1 align-top">Tempat/Tgl Lahir</td>
                                        <td class="align-top">:</td>
                                        <td class="align-top"><?php echo e($s->siswaProfile->tempat_lahir ?? '-'); ?>, <?php echo e($s->siswaProfile->tanggal_lahir ? \Carbon\Carbon::parse($s->siswaProfile->tanggal_lahir)->format('d M Y') : '-'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="qr-section">
                            <div class="qr-code-container">
                                <?php echo QrCode::size(data_get($config, 'config_json.qr_size', 70))->generate($s->identifier); ?>

                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-500 col-span-full">Tidak ada siswa untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\absensi-sekolah\resources\views/kelas/print_cards.blade.php ENDPATH**/ ?>