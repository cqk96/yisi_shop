<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

class ShopQrCodeController extends Controller
{
    public function show()
    {
        $url = route('shop.index');
        $logoPath = public_path('images/yisi-logo.png');

        $qrCode = new QrCode($url);
        $qrCode->setWriterByName('svg');
        $qrCode->setSize(520);
        $qrCode->setMargin(18);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setValidateResult(false);

        if (is_file($logoPath)) {
            $qrCode->setLogoPath($logoPath);
            $qrCode->setLogoSize(130, 84);
        }

        return response($qrCode->writeString(), 200, [
            'Content-Type' => $qrCode->getContentType(),
            'Content-Disposition' => 'inline; filename="shop-home-qr.svg"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
