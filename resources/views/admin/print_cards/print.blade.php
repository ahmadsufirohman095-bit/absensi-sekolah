<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Absensi</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet">
    <style>
        /* General card styling based on orientation */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(3.5in, 1fr)); /* 3.5 inch width for cards */
            gap: 10mm; /* Space between cards */
            padding: 10mm;
            box-sizing: border-box;
            page-break-after: always; /* Ensure new page for each batch of cards */
        }

        .card {
            border: 1px solid #ccc;
            padding: 10mm;
            box-sizing: border-box;
            background: #fff;
            position: relative;
            overflow: hidden; /* For watermark */
        }

        .card.portrait {
            width: 3.5in;  /* Approx 8.89 cm */
            height: 5in;   /* Approx 12.7 cm */
        }

        .card.landscape {
            width: 5in;    /* Approx 12.7 cm */
            height: 3.5in; /* Approx 8.89 cm */
        }

        .card-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .card-body {
            font-size: 0.8em;
        }

        .profile-image {
            width: {{ $config->config_json['photo_width'] ?? 70 }}px;
            height: {{ $config->config_json['photo_height'] ?? 90 }}px;
            object-fit: cover;
            border: 1px solid #eee;
            margin-bottom: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .qr-code {
            display: block;
            margin: 10px auto 0 auto;
            border: 1px solid #eee;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 3em;
            color: rgba(0, 0, 0, {{ $config->config_json['watermark_opacity'] ?? 0.1 }});
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            z-index: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .card-container {
                display: block; /* For print, allow natural flow */
                padding: 0;
                margin: 0;
                gap: 0;
            }
            .card {
                page-break-inside: avoid;
                margin: 5mm; /* Small margin for cutting */
                float: left; /* Arrange cards side by side */
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        @forelse ($users as $user)
            @php
                $userProfile = null;
                $mainIdentifier = $user->identifier;
                $displayName = $user->name;
                $displayRole = ucfirst($user->role);
                $displayFoto = null;
                $additionalFields = [];

                if ($user->role == 'admin' || $user->role == 'tu' || $user->role == 'other') {
                    $userProfile = $user->adminProfile;
                    if ($user->role == 'tu') {
                        $userProfile = $user->tuProfile;
                        $displayRole = 'Tata Usaha';
                    } elseif ($user->role == 'other' && $user->custom_role) {
                        $userProfile = $user->otherProfile;
                        $displayRole = $user->custom_role;
                    }

                    if ($userProfile) {
                        $displayFoto = $userProfile->foto ? asset('storage/' . $userProfile->foto) : asset('images/default-avatar.svg');
                        if (in_array('jabatan', $config->config_json['selected_fields'])) $additionalFields['Jabatan'] = $userProfile->jabatan ?? '-';
                        if (in_array('tempat_lahir', $config->config_json['selected_fields'])) $additionalFields['Tempat Lahir'] = $userProfile->tempat_lahir ?? '-';
                        if (in_array('jenis_kelamin', $config->config_json['selected_fields'])) $additionalFields['Jenis Kelamin'] = $userProfile->jenis_kelamin ?? '-';
                    }
                } elseif ($user->role == 'guru') {
                    $userProfile = $user->guruProfile;
                    if ($userProfile) {
                        $displayFoto = $userProfile->foto ? asset('storage/' . $userProfile->foto) : asset('images/default-avatar.svg');
                        if (in_array('jabatan', $config->config_json['selected_fields'])) $additionalFields['Jabatan'] = $userProfile->jabatan ?? '-';
                        if (in_array('tanggal_lahir', $config->config_json['selected_fields'])) $additionalFields['Tanggal Lahir'] = \Carbon\Carbon::parse($userProfile->tanggal_lahir)->translatedFormat('d F Y') ?? '-';
                        if (in_array('tempat_lahir', $config->config_json['selected_fields'])) $additionalFields['Tempat Lahir'] = $userProfile->tempat_lahir ?? '-';
                        if (in_array('jenis_kelamin', $config->config_json['selected_fields'])) $additionalFields['Jenis Kelamin'] = $userProfile->jenis_kelamin ?? '-';
                    }
                } elseif ($user->role == 'siswa') {
                    $userProfile = $user->siswaProfile;
                    if ($userProfile) {
                        $mainIdentifier = $userProfile->nis;
                        $displayFoto = $userProfile->foto ? asset('storage/' . $userProfile->foto) : asset('images/default-avatar.svg');
                        if (in_array('kelas', $config->config_json['selected_fields']) && $userProfile->kelas) $additionalFields['Kelas'] = $userProfile->kelas->nama_kelas;
                        if (in_array('tanggal_lahir', $config->config_json['selected_fields'])) $additionalFields['Tanggal Lahir'] = \Carbon\Carbon::parse($userProfile->tanggal_lahir)->translatedFormat('d F Y') ?? '-';
                        if (in_array('tempat_lahir', $config->config_json['selected_fields'])) $additionalFields['Tempat Lahir'] = $userProfile->tempat_lahir ?? '-';
                        if (in_array('jenis_kelamin', $config->config_json['selected_fields'])) $additionalFields['Jenis Kelamin'] = $userProfile->jenis_kelamin ?? '-';
                    }
                }
            @endphp
            <div class="card {{ $config->config_json['card_orientation'] ?? 'portrait' }}">
                @if (isset($config->config_json['watermark_enabled']) && $config->config_json['watermark_enabled'])
                    <div class="watermark">ABSENSI SEKOLAH</div>
                @endif
                <div class="card-header">
                    <img src="{{ asset('images/Logo_al-muttaqin.png') }}" alt="Logo Sekolah" style="height: 40px; margin-bottom: 5px;">
                    <h6>KARTU ABSENSI {{ strtoupper($displayRole) }}</h6>
                </div>
                <div class="card-body">
                    @if (in_array('foto', $config->config_json['selected_fields']))
                        <img src="{{ $displayFoto }}" alt="Foto Profil" class="profile-image">
                    @endif
                    <p><strong>Nama:</strong> {{ $displayName }}</p>
                    <p><strong>ID:</strong> {{ $mainIdentifier }}</p>
                    @foreach ($additionalFields as $label => $value)
                        <p><strong>{{ $label }}:</strong> {{ $value }}</p>
                    @endforeach
                    <img src="data:image/png;base64,{{ base64_encode(QrCode::size($config->config_json['qr_size'] ?? 70)->generate($user->identifier)) }}" alt="QR Code" class="qr-code">
                </div>
            </div>
        @empty
            <p>Tidak ada pengguna yang dipilih untuk dicetak.</p>
        @endforelse
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
