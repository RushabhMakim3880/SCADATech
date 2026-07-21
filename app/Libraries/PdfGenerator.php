<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\Fpdi;

class PdfGenerator
{
    private $pdfEngine; // Available options: 'mpdf', 'wkhtmltopdf', 'dompdf'
    private $wkhtmltopdfPath;
    private $margins;
    private $fontSize;
    private $pdfFontFamily;
    private $pdfFontColor = '#333'; // Default font color

    public function __construct($engine = 'mpdf')
    {
        $config = config('AppConfig');

        $this->margins = $config->pdfMargins ?? "5mm 5mm 5mm 5mm"; // Default margins
        $this->fontSize = $config->pdfFontSize ?? 12; // Default font size
        $this->pdfFontFamily = $config->pdfFontFamily ?? 'sans-serif'; // Default font family
        $this->pdfFontColor = $config->pdfFontColor ?? '#333'; // Default font color

        $this->pdfEngine = $config->pdfGenerator ?? $engine;
        $this->wkhtmltopdfPath = '/usr/bin/wkhtmltopdf'; // Adjust for Windows
    }

    public function generatePdf(string $htmlContent, string $letterheadPdf = null, array $prefixPdf = [], array $suffixPdf = [], bool $returnBase64 = false)
    {
        // insert css into htmlContent
        $font = "@font-face {
            font-family: '{$this->pdfFontFamily}';
            src: url('" . base_url("assets/fonts/$this->pdfFontFamily/Regular.ttf") . "') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: '{$this->pdfFontFamily}';
            src: url('" . base_url("assets/fonts/$this->pdfFontFamily/Bold.ttf") . "') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: '{$this->pdfFontFamily}';
            src: url('" . base_url("assets/fonts/$this->pdfFontFamily/Italic.ttf") . "') format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: '{$this->pdfFontFamily}';
            src: url('" . base_url("assets/fonts/$this->pdfFontFamily/BoldItalic.ttf ") . "') format('truetype');
            font-weight: bold;
            font-style: italic;
        }";

        // echo $font;
        // die();

        $htmlContent = str_replace('<head>', '<head><style>' . $font . '@page { margin: ' . $this->margins . '; }body {font-family: "' . $this->pdfFontFamily . '", sans-serif;font-size: ' . $this->fontSize . 'px;color: ' . $this->pdfFontColor . ';background: transparent;}</style>', $htmlContent);

        // echo $htmlContent;
        // die();

        if ($this->pdfEngine === 'wkhtmltopdf') {
            return $this->generateWithWkhtmltopdf($htmlContent, $letterheadPdf, $prefixPdf, $suffixPdf, $returnBase64);
        } elseif ($this->pdfEngine === 'mpdf') {
            return $this->generateWithMpdf($htmlContent, $letterheadPdf, $prefixPdf, $suffixPdf, $returnBase64);
        } elseif ($this->pdfEngine === 'dompdf') {
            return $this->generateWithDompdf($htmlContent, $letterheadPdf, $prefixPdf, $suffixPdf, $returnBase64);
        } else {
            return ['error' => 'Invalid PDF engine selected'];
        }
    }

    private function generateWithWkhtmltopdf(string $htmlContent, string $letterheadPdf = null, array $prefixPdf = [], array $suffixPdf = [], bool $returnBase64 = false)
    {
        $tempHtmlFile = tempnam(sys_get_temp_dir(), 'pdf') . '.html';
        $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf') . '.pdf';
        $transparentPdfFile = tempnam(sys_get_temp_dir(), 'pdf') . '_transparent.pdf';
        $errorLogFile = tempnam(sys_get_temp_dir(), 'pdf_err') . '.txt';

        file_put_contents($tempHtmlFile, $htmlContent);

        // Execute wkhtmltopdf and capture errors
        $command = "{$this->wkhtmltopdfPath} {$tempHtmlFile} {$tempPdfFile} 2> {$errorLogFile}";
        exec($command, $output, $returnVar);
        unlink($tempHtmlFile);

        // Capture errors if any
        $errorMessage = file_exists($errorLogFile) ? file_get_contents($errorLogFile) : 'Unknown error';
        unlink($errorLogFile);

        if ($returnVar !== 0 || !file_exists($tempPdfFile)) {
            return ['error' => 'PDF generation failed', 'details' => trim($errorMessage)];
        }

        // Overlay invoice on letterhead if required
        if ($letterheadPdf) {
            $withLetterHead = $this->overlayInvoiceOnLetterhead($tempPdfFile, $letterheadPdf);
            if (file_exists($tempPdfFile))
                unlink($tempPdfFile);
        } else {
            $withLetterHead = $tempPdfFile;
        }

        if (!empty($prefixPdf) or !empty($suffixPdf)) {
            $finalPdf = $this->mergePdfs(array_merge($prefixPdf, [$withLetterHead], $suffixPdf));
        } else {
            $finalPdf = file_get_contents($withLetterHead);
        }
        if (file_exists($withLetterHead))
            unlink($withLetterHead);

        return $returnBase64 ? base64_encode($finalPdf) : $finalPdf;
    }

    private function generateWithMpdf(string $htmlContent, string $letterheadPdf = null, array $prefixPdf = [], array $suffixPdf = [], bool $returnBase64 = false)
    {
        try {
            $mpdfConfig = [
                'tempDir' => WRITEPATH . 'temp' // Use CodeIgniter's writable directory
            ];

            $mpdf = new Mpdf($mpdfConfig);
            $mpdf->WriteHTML($htmlContent);

            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf') . '.pdf';
            $mpdf->Output($tempPdfFile, 'F');

            // Overlay invoice on letterhead if required
            if ($letterheadPdf) {
                $withLetterHead = $this->overlayInvoiceOnLetterhead($tempPdfFile, $letterheadPdf);
                if (file_exists($tempPdfFile))
                    unlink($tempPdfFile);
            } else {
                $withLetterHead = $tempPdfFile;
            }

            if (!empty($prefixPdf) or !empty($suffixPdf)) {
                $finalPdf = $this->mergePdfs(array_merge($prefixPdf, [$withLetterHead], $suffixPdf));
            } else {
                $finalPdf = file_get_contents($withLetterHead);
            }
            if (file_exists($withLetterHead))
                unlink($withLetterHead);

            return $returnBase64 ? base64_encode($finalPdf) : $finalPdf;
        } catch (\Exception $e) {
            return ['error' => 'PDF generation failed', 'details' => $e->getMessage()];
        }
    }

    private function generateWithDompdf(string $htmlContent, string $letterheadPdf = null, array $prefixPdf = [], array $suffixPdf = [], bool $returnBase64 = false)
    {
        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Allow loading external images

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($htmlContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf') . '.pdf';
            file_put_contents($tempPdfFile, $dompdf->output());

            // Overlay invoice on letterhead if required
            if ($letterheadPdf) {
                $withLetterHead = $this->overlayInvoiceOnLetterhead($tempPdfFile, $letterheadPdf);
                if (file_exists($tempPdfFile))
                    unlink($tempPdfFile);
            } else {
                $withLetterHead = $tempPdfFile;
            }


            // Merge PDFs if required
            if (!empty($prefixPdf) or !empty($suffixPdf)) {
                $finalPdf = $this->mergePdfs(array_merge($prefixPdf, [$withLetterHead], $suffixPdf));
                if (file_exists($withLetterHead))
                    unlink($withLetterHead);
            } else {
                $finalPdf = file_get_contents($withLetterHead);
            }
            if (file_exists($withLetterHead))
                unlink($withLetterHead);

            return $returnBase64 ? base64_encode($finalPdf) : $finalPdf;
        } catch (\Exception $e) {
            return ['error' => 'PDF generation failed', 'details' => $e->getMessage()];
        }
    }


    private function mergePdfs(array $pdfFiles)
    {
        $pdf = new FPDI();
        $outputFile = tempnam(sys_get_temp_dir(), 'merged_pdf') . '.pdf';

        foreach ($pdfFiles as $file) {
            if ($file && file_exists($file)) {
                $pageCount = $pdf->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplIdx);
                }
            }
        }

        $pdf->Output($outputFile, 'F');
        return file_get_contents($outputFile);
    }

    private function overlayInvoiceOnLetterhead(string $invoicePdf, string $letterheadPdf)
    {
        if (!file_exists($invoicePdf)) {
            return ['error' => 'Invoice PDF not found'];
        }

        $fpdi = new \setasign\Fpdi\Fpdi();
        $outputFile = tempnam(sys_get_temp_dir(), 'letterhead_invoice') . '.pdf';

        // Set source file once for invoice
        $contentPageCount = $fpdi->setSourceFile($invoicePdf);

        // Set source file once for letterhead
        $letterHeadPageCount = file_exists($letterheadPdf) ? $fpdi->setSourceFile($letterheadPdf) : null;
        $letterHeadTemplateId = $letterHeadPageCount ? $fpdi->importPage(1, 'MediaBox') : null;

        for ($pageNo = 1; $pageNo <= $contentPageCount; $pageNo++) {
            $fpdi->addPage();

            // Apply letterhead only if available
            if ($letterHeadTemplateId) {
                $fpdi->useTemplate($letterHeadTemplateId, 0, 0, 210, 297);
            }

            // Re-set invoice source file only once
            $fpdi->setSourceFile($invoicePdf);
            $contentPageId = $fpdi->importPage($pageNo, 'MediaBox', true);
            $fpdi->useTemplate($contentPageId, 0, 0, 210, 297);
        }

        $fpdi->Output($outputFile, 'F');
        return $outputFile;
    }
}
