<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * PdfService — wrapper barryvdh/laravel-dompdf untuk semua output cetak.
 * Ganti library custom CI3 `application/libraries/Pdf.php`.
 */
class PdfService
{
    /**
     * Generate PDF dari Blade view. Return: PDF download response (stream ke browser).
     *
     * @param string $bladeView  nama view Blade (tanpa prefix resources/views)
     * @param array  $data       data untuk view
     * @param string $filename   nama file PDF (tanpa .pdf)
     */
    public static function generate(string $bladeView, array $data = [], string $filename = 'cetak'): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($bladeView, $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Generate PDF dan simpan ke storage (untuk arsip / export email).
     * @return string path storage
     */
    public static function store(string $bladeView, array $data = [], string $dir = 'pdfs', string $filename = 'cetak'): string
    {
        $pdf = Pdf::loadView($bladeView, $data);
        $path = "storage/{$dir}/{$filename}.pdf";
        file_put_contents(base_path($path), $pdf->output());
        return $path;
    }
}
