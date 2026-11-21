<?php

namespace App\Services;

class SimpleXlsxExporter
{
    /**
     * Membuat konten biner XLSX valid tanpa dependensi pihak ketiga.
     *
     * @param string $sheetTitle Judul worksheet (maks 31 karakter)
     * @param array $headers Daftar nama kolom header
     * @param array $rows Daftar baris data (array of arrays)
     * @param float|int|null $totalAmount Total nominal untuk baris ringkasan di bawah (opsional)
     * @return string Konten biner file .xlsx
     */
    public static function create(string $sheetTitle, array $headers, array $rows, $totalAmount = null): string
    {
        $sheetTitle = mb_substr(preg_replace('/[\\\\\\/*?:\\[\\]]/', '', $sheetTitle), 0, 31) ?: 'Sheet1';

        $sheetXml = self::buildWorksheetXml($headers, $rows, $totalAmount);
        $workbookXml = self::buildWorkbookXml($sheetTitle);
        $workbookRelsXml = self::buildWorkbookRelsXml();
        $stylesXml = self::buildStylesXml();
        $contentTypesXml = self::buildContentTypesXml();
        $rootRelsXml = self::buildRootRelsXml();

        $files = [
            '_rels/.rels' => $rootRelsXml,
            '[Content_Types].xml' => $contentTypesXml,
            'xl/_rels/workbook.xml.rels' => $workbookRelsXml,
            'xl/workbook.xml' => $workbookXml,
            'xl/styles.xml' => $stylesXml,
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ];

        return self::packZip($files);
    }

    protected static function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
    }

    protected static function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '</Types>';
    }

    protected static function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '</Relationships>';
    }

    protected static function buildWorkbookXml(string $sheetTitle): string
    {
        $escapedTitle = htmlspecialchars($sheetTitle, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="24000" windowHeight="14000"/></bookViews>' .
            '<sheets>' .
            '<sheet name="' . $escapedTitle . '" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .
            '</workbook>';
    }

    protected static function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="3">' .
            '<font><name val="Calibri"/><sz val="11"/><color theme="1"/></font>' .
            '<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>' .
            '<font><b/><name val="Calibri"/><sz val="11"/><color theme="1"/></font>' .
            '</fonts>' .
            '<fills count="4">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FFF53003"/></patternFill></fill>' . // Smart Savings Theme Color
            '<fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/></patternFill></fill>' . // Light Gray for Totals
            '</fills>' .
            '<borders count="2">' .
            '<border><left/><right/><top/><bottom/></border>' .
            '<border>' .
            '<left style="thin"><color rgb="FFD1D5DB"/></left>' .
            '<right style="thin"><color rgb="FFD1D5DB"/></right>' .
            '<top style="thin"><color rgb="FFD1D5DB"/></top>' .
            '<bottom style="thin"><color rgb="FFD1D5DB"/></bottom>' .
            '</border>' .
            '</borders>' .
            '<cellStyleXfs count="1">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
            '</cellStyleXfs>' .
            '<cellXfs count="6">' .
            '<!-- 0: Standard Text -->' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>' .
            '<!-- 1: Header Row -->' .
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' .
            '<!-- 2: Centered Text -->' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' .
            '<!-- 3: Currency / Number (Thousand Separator) -->' .
            '<xf numFmtId="3" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' .
            '<!-- 4: Total Label (Bold, Light Gray) -->' .
            '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' .
            '<!-- 5: Total Amount (Bold Currency, Light Gray) -->' .
            '<xf numFmtId="3" fontId="2" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' .
            '</cellXfs>' .
            '</styleSheet>';
    }

    protected static function buildWorksheetXml(array $headers, array $rows, $totalAmount = null): string
    {
        $numCols = count($headers);

        $colWidths = [
            1 => 6,   // No
            2 => 15,  // Tanggal
            3 => 15,  // NISN
            4 => 25,  // Nama Siswa
            5 => 14,  // Kelas
            6 => 16,  // Kategori
            7 => 28,  // Deskripsi
            8 => 20,  // Jumlah
        ];

        $colsXml = '<cols>';
        for ($c = 1; $c <= $numCols; $c++) {
            $w = $colWidths[$c] ?? 16;
            $colsXml .= '<col min="' . $c . '" max="' . $c . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetData = '<sheetData>';

        // Row 1: Headers
        $sheetData .= '<row r="1" ht="26" customHeight="1">';
        foreach ($headers as $colIdx => $headerText) {
            $coord = self::getColLetter($colIdx) . '1';
            $escaped = htmlspecialchars($headerText, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $sheetData .= '<c r="' . $coord . '" t="inlineStr" s="1"><is><t>' . $escaped . '</t></is></c>';
        }
        $sheetData .= '</row>';

        // Data Rows
        $currentRow = 2;
        foreach ($rows as $row) {
            $sheetData .= '<row r="' . $currentRow . '" ht="20" customHeight="1">';
            foreach ($row as $colIdx => $val) {
                $coord = self::getColLetter($colIdx) . $currentRow;

                if (is_numeric($val) && !str_starts_with((string)$val, '0')) {
                    // Angka murni (No urut atau Nominal Rupiah)
                    // Jika kolom terakhir (Jumlah Uang Masuk), pakai format currency (s=3), kolom No pakai center (s=2)
                    $styleId = ($colIdx === $numCols - 1) ? 3 : 2;
                    $sheetData .= '<c r="' . $coord . '" s="' . $styleId . '"><v>' . $val . '</v></c>';
                } else {
                    // String / Teks
                    // Kolom No (0), Tanggal (1), NISN (2), Kelas (4) -> Center (s=2), lainnya left (s=0)
                    $styleId = in_array($colIdx, [0, 1, 2, 4]) ? 2 : 0;
                    $escaped = htmlspecialchars((string)($val ?? '-'), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $sheetData .= '<c r="' . $coord . '" t="inlineStr" s="' . $styleId . '"><is><t>' . $escaped . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
            $currentRow++;
        }

        // Summary Total Row jika ada
        $mergeXml = '';
        if ($totalAmount !== null && $numCols >= 2) {
            $sheetData .= '<row r="' . $currentRow . '" ht="24" customHeight="1">';

            // Sel label total
            for ($c = 0; $c < $numCols - 1; $c++) {
                $coord = self::getColLetter($c) . $currentRow;
                $label = ($c === 0) ? 'TOTAL KESELURUHAN' : '';
                $sheetData .= '<c r="' . $coord . '" t="inlineStr" s="4"><is><t>' . $label . '</t></is></c>';
            }

            // Sel total nominal
            $lastColCoord = self::getColLetter($numCols - 1) . $currentRow;
            $sheetData .= '<c r="' . $lastColCoord . '" s="5"><v>' . $totalAmount . '</v></c>';
            $sheetData .= '</row>';

            // Merge kolom A sampai kolom sebelum terakhir
            $mergeRef = 'A' . $currentRow . ':' . self::getColLetter($numCols - 2) . $currentRow;
            $mergeXml = '<mergeCells count="1"><mergeCell ref="' . $mergeRef . '"/></mergeCells>';
        }

        $sheetData .= '</sheetData>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            $colsXml .
            $sheetData .
            $mergeXml .
            '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>' .
            '</worksheet>';
    }

    public static function getColLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex >= 0) {
            $letter = chr($colIndex % 26 + 65) . $letter;
            $colIndex = intdiv($colIndex, 26) - 1;
        }
        return $letter;
    }

    protected static function packZip(array $files): string
    {
        // 1. Coba gunakan ZipArchive bawaan PHP jika terpasang
        if (class_exists('\\ZipArchive')) {
            $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
            $zip = new \ZipArchive();
            if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($files as $name => $content) {
                    $zip->addFromString($name, $content);
                }
                $zip->close();
                $data = file_get_contents($tempFile);
                @unlink($tempFile);
                if ($data !== false && strlen($data) > 0) {
                    return $data;
                }
            }
        }

        // 2. Fallback: Pure PHP ZIP generator (PKWARE format)
        return self::createPurePhpZip($files);
    }

    protected static function createPurePhpZip(array $files): string
    {
        $zipData = '';
        $centralDir = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $uSize = strlen($content);
            $crc = crc32($content);

            if (function_exists('gzdeflate')) {
                $compressed = gzdeflate($content, 6);
                $cSize = strlen($compressed);
                $method = 8; // Deflate
            } else {
                $compressed = $content;
                $cSize = $uSize;
                $method = 0; // Store
            }

            $dosTime = self::getDosTime(time());
            $nameLen = strlen($name);

            // Local File Header (30 byte)
            $header = pack(
                'VvvvVVVVvv',
                0x04034b50,
                20,
                0,
                $method,
                $dosTime,
                $crc,
                $cSize,
                $uSize,
                $nameLen,
                0
            );

            $zipData .= $header . $name . $compressed;

            // Central Directory Entry (46 byte)
            $cdEntry = pack(
                'VvvvvVVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                $method,
                $dosTime,
                $crc,
                $cSize,
                $uSize,
                $nameLen,
                0,
                0,
                0,
                0,
                0x20,
                $offset
            );

            $centralDir .= $cdEntry . $name;
            $offset = strlen($zipData);
        }

        $cdSize = strlen($centralDir);
        $cdOffset = strlen($zipData);

        // End of Central Directory Record (22 byte)
        $eocd = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            count($files),
            count($files),
            $cdSize,
            $cdOffset,
            0
        );

        return $zipData . $centralDir . $eocd;
    }

    protected static function getDosTime(int $timestamp): int
    {
        $date = getdate($timestamp);
        return (($date['year'] - 1980) << 25)
             | ($date['mon'] << 21)
             | ($date['mday'] << 16)
             | ($date['hours'] << 11)
             | ($date['minutes'] << 5)
             | ($date['seconds'] >> 1);
    }
}
