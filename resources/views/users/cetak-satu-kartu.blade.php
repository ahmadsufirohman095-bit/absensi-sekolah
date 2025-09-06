<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .card {
            /* KTP/ID-1 standard: 85.60 mm × 53.98 mm */
            /* At 96 DPI: 323.5px x 204.0px (approx) */
            width: 323.5px;
            height: 204.0px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
            border: 1px solid #ccc; /* Add a subtle border for definition */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle shadow */
            margin: 5px; /* Small margin between cards for visual separation in PDF */
        }
        .card-watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ logo_url() }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 70%; /* Adjust size as needed */
            opacity: {{ data_get($config->config_json, 'watermark_enabled', false) ? (data_get($config->config_json, 'watermark_opacity', 0.1)) : 0 }}; /* Adjust transparency based on config */
            z-index: 0; /* Ensure it's behind content */
        }
        .card-header {
            background: linear-gradient(to right, #1e3a8a, #3b82f6);
            color: white;
            padding: 5px 8px; /* Adjusted padding */
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1; /* Ensure header is above watermark */
        }
        .card-header img {
            width: 35px; /* Smaller logo in header */
            height: 35px;
            margin-right: 8px;
        }
        .card-header p {
            font-size: 10px; /* Smaller font size */
            margin: 0;
        }
        .card-header .title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .card-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.7); /* More opaque footer */
            backdrop-filter: blur(2px);
            font-size: 8px; /* Smaller font size */
            text-align: center;
            padding: 2px 0;
            position: relative;
            z-index: 1; /* Ensure footer is above watermark */
        }
        .qr-code-container {
            border: 2px solid white; /* Smaller border */
            border-radius: 6px; /* Smaller border radius */
            box-shadow: 0 3px 5px rgba(0,0,0,0.1); /* Smaller shadow */
            padding: 2px; /* Smaller padding */
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Center cards in the container */
            padding: 5px; /* Reduced padding */
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            padding: 8px; /* Adjusted padding */
            position: relative;
            z-index: 1; /* Ensure body is above watermark */
        }
        .card-body .photo {
            flex-shrink: 0;
            margin-right: 8px; /* Reduced margin */
        }
        .card-body .photo img {
            width: 70px; /* Smaller photo */
            height: 90px;
            object-fit: cover;
            border: 1px solid white; /* Smaller border */
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-body .info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-body .info table {
            font-size: 9px; /* Smaller font size */
            width: 100%;
        }
        .card-body .info table td {
            padding-bottom: 1px; /* Smaller padding */
        }
        .card-body .info .qr-section {
            display: flex;
            justify-content: flex-end; /* Align QR code to the right */
            align-self: flex-end; /* Push QR code to the bottom */
            margin-top: 3px; /* Reduced space above QR */
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .card {
                border: 1px solid #eee;
            }
            @page {
                size: 323.5px 204.0px; /* Adjusted page size for print */
                margin: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-200 flex justify-center items-center min-h-screen">
    <div class="card-container">
        <div class="card rounded-lg">
            <div class="card-watermark"></div> <!-- Watermark layer -->
            <!-- Header -->
            <div class="card-header">
                <img src="{{ logo_url() }}" alt="Logo Sekolah">
                <div>
                    <p class="title">Kartu Tanda Pelajar</p>
                    <p>{{ setting('nama_sekolah', 'Nama Sekolah') }}</p>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Foto -->
                @if (in_array('foto', data_get($config->config_json, 'selected_fields', [])))
                    <div class="photo">
                        <img src="{{ $user->foto_url }}" alt="Foto Siswa">
                    </div>
                @endif
                <!-- Info & QR -->
                <div class="info">
                    <table>
                        <tbody>
                            @if (in_array('name', data_get($config->config_json, 'selected_fields', [])))
                                <tr>
                                    <td class="font-semibold pr-1 align-top">Nama</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top font-bold text-gray-800">{{ strtoupper($user->name) }}</td>
                                </tr>
                            @endif
                            @if (in_array('nis', data_get($config->config_json, 'selected_fields', [])))
                                <tr>
                                    <td class="font-semibold pr-1 align-top">NIS</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">{{ $user->identifier }}</td>
                                </tr>
                            @endif
                            @if (in_array('kelas', data_get($config->config_json, 'selected_fields', [])))
                                <tr>
                                    <td class="font-semibold pr-1 align-top">Kelas</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">{{ $user->siswaProfile->kelas->nama_kelas ?? '-' }}</td>
                                </tr>
                            @endif
                            @if (in_array('tanggal_lahir', data_get($config->config_json, 'selected_fields', [])))
                                <tr>
                                    <td class="font-semibold pr-1 align-top">Lahir</td>
                                    <td class="align-top">:</td>
                                    <td class="align-top">{{ $user->siswaProfile->tempat_lahir ?? '-' }}, {{ $user->siswaProfile->tanggal_lahir ? \Carbon\Carbon::parse($user->siswaProfile->tanggal_lahir)->format('d M Y') : '-' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="qr-section">
                        <div class="qr-code-container">
                            {!! QrCode::size(data_get($config->config_json, 'qr_size', 70))->generate($user->identifier) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer">
                <p>{{ setting('alamat_sekolah', 'Alamat Sekolah') }}</p>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="no-print mt-6 text-center">
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md">Cetak</button>
            <a href="{{ url()->previous() }}" class="ml-4 text-gray-700 hover:text-gray-900">Kembali</a>
        </div>
    </div>
</body>
</html>

