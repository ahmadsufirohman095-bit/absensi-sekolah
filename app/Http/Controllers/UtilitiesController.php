<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

class UtilitiesController extends Controller
{
    /**
     * Generate a QR code image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateQrCode(Request $request)
    {
        $data = $request->input('data', 'QR Code Pratinjau');
        $size = $request->input('size', 150);
        $margin = $request->input('margin', 1);

        // Generate QR code as SVG string
        $svg = QrCode::format('svg')
                     ->size($size)
                     ->margin($margin)
                     ->generate($data);

        return Response::make($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
