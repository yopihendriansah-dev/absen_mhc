<?php

namespace App\Services;

use App\Models\Registration;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

class RegistrationQrCodeService
{
    public function generate(Registration $registration): string
    {
        $registration->loadMissing('event');

        $canvas = imagecreatetruecolor(420, 540);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 17, 24, 39);
        $navy = imagecolorallocate($canvas, 16, 75, 104);
        $teal = imagecolorallocate($canvas, 21, 155, 131);
        $muted = imagecolorallocate($canvas, 93, 125, 134);
        imagefill($canvas, 0, 0, $white);

        $this->drawQrCode($canvas, $registration->registration_code, 30, 20, 360, $black);

        $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        $boldFont = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        $drawCentered = function (string $text, int $y, int $size, int $color, bool $bold = false) use ($canvas, $font, $boldFont): void {
            $fontPath = $bold ? $boldFont : $font;

            if (is_file($fontPath) && function_exists('imagettfbbox')) {
                $box = imagettfbbox($size, 0, $fontPath, $text);
                $width = abs($box[2] - $box[0]);
                imagettftext($canvas, $size, 0, (int) ((420 - $width) / 2), $y, $color, $fontPath, $text);

                return;
            }

            $width = imagefontwidth(5) * strlen($text);
            imagestring($canvas, 5, max((420 - $width) / 2, 0), $y - imagefontheight(5), $text, $color);
        };

        $drawCentered('MHC COMMUNITY', 418, 13, $teal, true);
        $drawCentered(mb_strimwidth($registration->name, 0, 38, '...'), 450, 19, $navy, true);
        $drawCentered('ID: '.$registration->registration_code, 478, 13, $muted);
        $drawCentered(mb_strimwidth($registration->event->name, 0, 48, '...'), 510, 12, $muted);

        ob_start();
        imagepng($canvas);
        $image = ob_get_clean();
        imagedestroy($canvas);

        return $image;
    }

    private function drawQrCode($canvas, string $content, int $left, int $top, int $size, int $darkColor): void
    {
        $matrix = Encoder::encode($content, ErrorCorrectionLevel::M())->getMatrix();
        $margin = 1;
        $modules = $matrix->getWidth() + ($margin * 2);
        $moduleSize = max((int) floor($size / $modules), 1);
        $qrSize = $modules * $moduleSize;
        $offsetX = $left + (int) floor(($size - $qrSize) / 2);
        $offsetY = $top + (int) floor(($size - $qrSize) / 2);

        for ($y = 0; $y < $matrix->getHeight(); $y++) {
            for ($x = 0; $x < $matrix->getWidth(); $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }

                $moduleX = $offsetX + (($x + $margin) * $moduleSize);
                $moduleY = $offsetY + (($y + $margin) * $moduleSize);

                imagefilledrectangle(
                    $canvas,
                    $moduleX,
                    $moduleY,
                    $moduleX + $moduleSize - 1,
                    $moduleY + $moduleSize - 1,
                    $darkColor
                );
            }
        }
    }
}
