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

        $canvas = imagecreatetruecolor(420, 600);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 17, 24, 39);
        $navy = imagecolorallocate($canvas, 16, 75, 104);
        $teal = imagecolorallocate($canvas, 21, 155, 131);
        $muted = imagecolorallocate($canvas, 93, 125, 134);
        imagefill($canvas, 0, 0, $white);

        $this->drawQrCode($canvas, $registration->registration_code, 30, 20, 360, $black);

        // Font chain: Instrument Sans (sesuai web) bundled di repo -> DejaVu bundled -> DejaVu sistem.
        // Ini menjamin render konsisten antara local & production, tidak tergantung font OS server.
        $fontCandidates = [
            'regular' => [
                resource_path('fonts/InstrumentSans-Variable.ttf'),
                resource_path('fonts/DejaVuSans.ttf'),
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ],
            'bold' => [
                resource_path('fonts/InstrumentSans-Variable.ttf'),
                resource_path('fonts/DejaVuSans-Bold.ttf'),
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ],
        ];
        $resolveFont = static function (string $weight) use ($fontCandidates): ?string {
            foreach ($fontCandidates[$weight] ?? [] as $path) {
                if (is_file($path) && function_exists('imagettfbbox') && @imagettfbbox(12, 0, $path, 'Ag') !== false) {
                    return $path;
                }
            }

            return null;
        };
        $font = $resolveFont('regular');
        $boldFont = $resolveFont('bold') ?? $font;

        // Normalisasi tanda baca "pintar" agar tidak jadi karakter aneh (Ã¢/â)
        // di server yang font/GD-nya tidak mendukung glyph tersebut.
        $normalize = static fn (?string $value): string => str_replace(
            ["\u{201C}", "\u{201D}", "\u{201E}", "\u{2018}", "\u{2019}", "\u{201A}", "\u{00AB}", "\u{00BB}", "\u{2013}", "\u{2014}", "\u{2026}"],
            ['"', '"', '"', "'", "'", "'", '"', '"', '-', '--', '...'],
            (string) $value,
        );
        $drawCentered = function (string $text, int $y, int $size, int $color, bool $bold = false) use ($canvas, $font, $boldFont): void {
            $fontPath = $bold ? $boldFont : $font;

            if (is_string($fontPath) && is_file($fontPath) && function_exists('imagettftext')) {
                $box = imagettfbbox($size, 0, $fontPath, $text);
                $width = abs($box[2] - $box[0]);
                imagettftext($canvas, $size, 0, (int) ((420 - $width) / 2), $y, $color, $fontPath, $text);

                return;
            }

            // Fallback Latin-1 agar tidak jadi karakter aneh bila TTF tidak tersedia.
            $safe = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            $width = imagefontwidth(5) * strlen($safe);
            imagestring($canvas, 5, max((420 - $width) / 2, 0), $y - imagefontheight(5), $safe, $color);
        };

        $drawWrapped = function (string $text, int $startY, int $size, int $color, bool $bold = false, int $maxWidth = 380, int $lineHeight = 20, int $maxLines = 3) use ($canvas, $font, $boldFont): int {
            $fontPath = $bold ? $boldFont : $font;
            $useTtf = is_string($fontPath) && is_file($fontPath) && function_exists('imagettftext');
            $words = preg_split('/\s+/u', trim($text)) ?: [];
            $lines = [];
            $current = '';

            foreach ($words as $word) {
                $candidate = $current === '' ? $word : $current.' '.$word;

                if ($useTtf) {
                    $box = imagettfbbox($size, 0, $fontPath, $candidate);
                    $width = abs($box[2] - $box[0]);
                } else {
                    $width = imagefontwidth(5) * strlen(mb_convert_encoding($candidate, 'ISO-8859-1', 'UTF-8'));
                }

                if ($width > $maxWidth && $current !== '') {
                    $lines[] = $current;
                    $current = $word;
                } else {
                    $current = $candidate;
                }
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            if (count($lines) > $maxLines) {
                $lines = array_slice($lines, 0, $maxLines);
                $lines[$maxLines - 1] = rtrim(mb_strimwidth($lines[$maxLines - 1], 0, 44, '...'));
            }

            $y = $startY;
            foreach ($lines as $line) {
                $drawLine = $line;
                if ($useTtf) {
                    $box = imagettfbbox($size, 0, $fontPath, $line);
                    $width = abs($box[2] - $box[0]);
                    imagettftext($canvas, $size, 0, (int) ((420 - $width) / 2), $y, $color, $fontPath, $drawLine);
                } else {
                    $safe = mb_convert_encoding($drawLine, 'ISO-8859-1', 'UTF-8');
                    $width = imagefontwidth(5) * strlen($safe);
                    imagestring($canvas, 5, max((420 - $width) / 2, 0), $y - imagefontheight(5), $drawLine, $color);
                }
                $y += $lineHeight;
            }

            return $y;
        };

        $drawCentered('MHC COMMUNITY', 418, 13, $teal, true);
        $nameEndY = $drawWrapped($normalize(mb_strimwidth($registration->name, 0, 80)), 452, 19, $navy, true, 380, 26, 2);
        $drawCentered('ID: '.$registration->registration_code, $nameEndY + 24, 13, $muted);
        $drawWrapped($normalize($registration->event->name), $nameEndY + 50, 12, $muted, false, 380, 18, 3);

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
