<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * ExcelService — wrapper phpoffice/phpspreadsheet untuk output Excel.
 * Ganti library custom CI3 `application/libraries/PhpSpreadsheet.php`.
 */
class ExcelService
{
    /**
     * Export data ke Excel (download).
     *
     * @param array $rows     data baris (array of array)
     * @param array $headers  label kolom
     * @param string $filename nama file (tanpa .xlsx)
     * @param string $sheet   nama sheet
     */
    public static function export(array $rows, array $headers, string $filename = 'laporan', string $sheet = 'Data'): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle($sheet);

        // Header
        foreach ($headers as $i => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $ws->setCellValue($col . '1', $h);
        }
        if (count($headers)) {
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $ws->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
        }

        // Data
        foreach ($rows as $ri => $row) {
            foreach (array_values((array) $row) as $ci => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
                $ws->setCellValue($col . ($ri + 2), $val);
            }
        }

        $tmp = tempnam(sys_get_temp_dir(), 'excel_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($tmp);
        return response()->download($tmp, $filename . '.xlsx')->deleteFileAfterSend(true);
    }

    /**
     * Alternatif pakai collection of array (key => value per baris).
     */
    public static function exportRows(array $rows, string $filename = 'laporan', string $sheet = 'Data'): BinaryFileResponse
    {
        $headers = !empty($rows[0]) ? array_keys((array) $rows[0]) : [];
        $flat = array_map(fn ($r) => array_values((array) $r), $rows);
        return self::export($flat, $headers, $filename, $sheet);
    }
}
