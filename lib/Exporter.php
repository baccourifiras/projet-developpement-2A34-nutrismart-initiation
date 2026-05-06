<?php
/**
 * ============================================================
 *  NutriSmart - Exporter
 *  /lib/Exporter.php
 *
 *  Trois exports en PHP natif (zéro dépendance Composer) :
 *   - CSV   : SplFileObject->fputcsv (UTF-8 + BOM pour Excel)
 *   - Excel : XML SpreadsheetML (s'ouvre dans Excel/LibreOffice)
 *   - PDF   : génération minimale d'un PDF tabulaire (PDF brut),
 *             suffisant pour un export rapide. Pour un PDF design,
 *             on peut brancher FPDF/Dompdf plus tard sans toucher
 *             au reste du module.
 * ============================================================
 */

class Exporter
{
    /**
     * Force le téléchargement d'un fichier CSV.
     *
     * @param string  $filename  ex: 'recettes.csv'
     * @param array   $headers   ex: ['ID','Nom','Durée']
     * @param array   $rows      ex: [['1','Soupe','30'], ...]
     */
    public static function csv(string $filename, array $headers, array $rows): void
    {
        self::sendDownloadHeaders($filename, 'text/csv; charset=utf-8');
        // BOM UTF-8 -> Excel reconnaît l'encodage et garde les accents
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        fputcsv($out, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($out, $row, ';');
        }
        fclose($out);
        exit;
    }

    /**
     * Excel : SpreadsheetML XML 2003. S'ouvre dans Excel/LibreOffice,
     * supporte les accents UTF-8 et le formatage des colonnes.
     */
    public static function excel(string $filename, array $headers, array $rows, string $sheetName = 'Export'): void
    {
        self::sendDownloadHeaders($filename, 'application/vnd.ms-excel; charset=utf-8');

        $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES | ENT_XML1, 'UTF-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                       xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        echo '<Styles>
                <Style ss:ID="hdr">
                  <Font ss:Bold="1" ss:Color="#FFFFFF"/>
                  <Interior ss:Color="#17995F" ss:Pattern="Solid"/>
                  <Alignment ss:Vertical="Center"/>
                </Style>
              </Styles>' . "\n";
        echo '<Worksheet ss:Name="' . $esc($sheetName) . '"><Table>' . "\n";

        // En-tête
        echo '<Row>';
        foreach ($headers as $h) {
            echo '<Cell ss:StyleID="hdr"><Data ss:Type="String">' . $esc($h) . '</Data></Cell>';
        }
        echo '</Row>' . "\n";

        // Données
        foreach ($rows as $row) {
            echo '<Row>';
            foreach ($row as $cell) {
                $type = is_numeric($cell) ? 'Number' : 'String';
                echo '<Cell><Data ss:Type="' . $type . '">' . $esc($cell) . '</Data></Cell>';
            }
            echo '</Row>' . "\n";
        }

        echo '</Table></Worksheet></Workbook>';
        exit;
    }

    /**
     * PDF généré à la main (pas de dépendance externe).
     * Suffisant pour un rapport tabulaire simple en A4 portrait.
     * Note : utilise la police Helvetica intégrée -> les accents
     *        sont convertis en équivalents ASCII via iconv.
     */
    public static function pdf(string $filename, string $title, array $headers, array $rows): void
    {
        $clean = function (string $s): string {
            // Helvetica de base ne gère pas l'UTF-8 : on translittère
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            return $t !== false ? $t : $s;
        };

        $pdf = new SimplePdfBuilder();
        $pdf->setFont('Helvetica-Bold', 16);
        $pdf->writeText($clean($title), 50, 800);

        $pdf->setFont('Helvetica', 9);
        $pdf->writeText('Genere le ' . date('d/m/Y H:i'), 50, 782);

        // Largeur A4 = 595 ; on commence à x=40, on garde 40 de marge à droite
        $colCount = max(1, count($headers));
        $usable = 595 - 80;
        $colW = (int)floor($usable / $colCount);

        // En-tête tableau
        $y = 750;
        $pdf->setFont('Helvetica-Bold', 10);
        $pdf->fillRect(40, $y - 4, $usable, 18, 0.09, 0.60, 0.37); // vert
        foreach ($headers as $i => $h) {
            $pdf->setColor(1, 1, 1);
            $pdf->writeText($clean($h), 44 + $i * $colW, $y);
        }
        $pdf->setColor(0, 0, 0);

        // Lignes
        $pdf->setFont('Helvetica', 9);
        $y -= 22;
        foreach ($rows as $rowIndex => $row) {
            if ($y < 60) {
                $pdf->newPage();
                $y = 800;
            }
            // Zébrage
            if ($rowIndex % 2 === 0) {
                $pdf->fillRect(40, $y - 4, $usable, 16, 0.96, 0.99, 0.97);
            }
            foreach ($row as $i => $cell) {
                $text = $clean((string)$cell);
                if (mb_strlen($text) > 38) {
                    $text = mb_substr($text, 0, 35) . '...';
                }
                $pdf->writeText($text, 44 + $i * $colW, $y);
            }
            $y -= 16;
        }

        $body = $pdf->build();

        self::sendDownloadHeaders($filename, 'application/pdf');
        header('Content-Length: ' . strlen($body));
        echo $body;
        exit;
    }

    private static function sendDownloadHeaders(string $filename, string $contentType): void
    {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}


/**
 * ------------------------------------------------------------
 *  Builder PDF minimal (PDF 1.4) - usage interne uniquement.
 *  Génère un document avec texte + rectangles colorés.
 *  Police : Helvetica / Helvetica-Bold (Type1, intégrée).
 * ------------------------------------------------------------
 */
class SimplePdfBuilder
{
    private array $pages = [];
    private string $current = '';
    private string $font = 'F1';
    private int    $size = 12;
    private float  $r = 0, $g = 0, $b = 0;

    public function __construct()
    {
        $this->newPage();
    }

    public function newPage(): void
    {
        if ($this->current !== '') $this->pages[] = $this->current;
        $this->current = "q\n";
    }

    public function setFont(string $name, int $size): void
    {
        $this->font = $name === 'Helvetica-Bold' ? 'F2' : 'F1';
        $this->size = $size;
    }

    public function setColor(float $r, float $g, float $b): void
    {
        $this->r = $r; $this->g = $g; $this->b = $b;
        $this->current .= sprintf("%.2F %.2F %.2F rg\n", $r, $g, $b);
    }

    public function fillRect(float $x, float $y, float $w, float $h, float $r, float $g, float $b): void
    {
        $this->current .= sprintf(
            "q %.2F %.2F %.2F rg %.2F %.2F %.2F %.2F re f Q\n",
            $r, $g, $b, $x, $y, $w, $h
        );
    }

    public function writeText(string $text, float $x, float $y): void
    {
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $this->current .= sprintf(
            "BT /%s %d Tf %.2F %.2F %.2F rg %.2F %.2F Td (%s) Tj ET\n",
            $this->font, $this->size, $this->r, $this->g, $this->b, $x, $y, $escaped
        );
    }

    public function build(): string
    {
        $this->pages[] = $this->current . "Q\n";

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        $kids = [];
        $pageObjStart = 3;
        $contentObjStart = $pageObjStart + count($this->pages);

        // Page objects
        for ($i = 0; $i < count($this->pages); $i++) {
            $pageObj = $pageObjStart + $i;
            $contentObj = $contentObjStart + $i;
            $kids[] = "$pageObj 0 R";
            $objects[$pageObj] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] "
                               . "/Resources << /Font << /F1 " . ($contentObjStart + count($this->pages)) . " 0 R "
                               . "/F2 " . ($contentObjStart + count($this->pages) + 1) . " 0 R >> >> "
                               . "/Contents $contentObj 0 R >>";
        }

        // Pages tree
        $objects[2] = "<< /Type /Pages /Kids [" . implode(' ', $kids) . "] /Count " . count($this->pages) . " >>";

        // Content streams
        for ($i = 0; $i < count($this->pages); $i++) {
            $stream = $this->pages[$i];
            $objects[$contentObjStart + $i] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }

        // Fonts
        $fontF1 = $contentObjStart + count($this->pages);
        $fontF2 = $fontF1 + 1;
        $objects[$fontF1] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objects[$fontF2] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";

        // Build the file
        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $content) {
            $offsets[$id] = strlen($out);
            $out .= "$id 0 obj\n$content\nendobj\n";
        }

        $xrefStart = strlen($out);
        $out .= "xref\n0 " . (count($objects) + 1) . "\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $out .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xrefStart\n%%EOF";

        return $out;
    }
}
