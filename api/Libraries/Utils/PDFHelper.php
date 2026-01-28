<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFHelper
{
    public static function generateReceiptHTML($r)
    {
        // Formatear montos
        $totalFormatted = "$ " . number_format($r['total'], 0, ',', '.');
        $fecha = date("d/m/Y", strtotime($r['fecha_pago']));

        // Determinar logo (similar a helpers.js)
        $logo = $r['logo_torneo'] ?: $r['logo_liga'] ?: "default_torneo.png";
        $folder = ($r['logo_torneo']) ? 'torneos' : ($r['logo_liga'] ? 'logos' : 'torneos');

        // Para PDF necesitamos ruta física o base64
        $logoPath = dirname(__DIR__, 2) . "/app/assets/images/$folder/$logo";
        $logoBase64 = "";
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: sans-serif; color: #333; line-height: 1.4; padding: 20px; }
                .header { border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px; overflow: hidden; }
                .logo { float: left; width: 80px; height: 80px; }
                .header-text { float: left; margin-left: 20px; }
                .header-text h2 { margin: 0; color: #1a1a1a; text-transform: uppercase; font-size: 18px; }
                .header-text h4 { margin: 2px 0; color: #666; font-size: 14px; }
                .badge { background: #0d6efd; color: white; padding: 2px 8px; font-size: 9px; border-radius: 3px; font-weight: bold; }
                .print-date { float: right; text-align: right; font-size: 10px; color: #999; }
                
                .title-section { overflow: hidden; margin-bottom: 20px; }
                .receipt-title { float: left; }
                .receipt-title h1 { margin: 0; color: #0d6efd; font-size: 22px; }
                .receipt-title span { font-weight: bold; color: #666; }
                .receipt-date { float: right; font-weight: bold; margin-top: 5px; }
                
                .info-row { overflow: hidden; margin-bottom: 20px; background: #f8f9fa; padding: 10px; border-radius: 5px; }
                .info-col { float: left; width: 50%; }
                .label { font-size: 10px; text-uppercase: uppercase; color: #999; font-weight: bold; }
                .value { font-size: 14px; font-weight: bold; }
                .total { color: #198754; font-size: 20px; }
                
                table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                th { background: #f1f1f1; text-align: left; padding: 8px; font-size: 11px; border: 1px solid #ddd; }
                td { padding: 8px; font-size: 12px; border: 1px solid #ddd; }
                .text-right { text-align: right; }
                
                .footer { margin-top: 50px; overflow: hidden; }
                .footer-col { float: left; width: 45%; }
                .signature-box { border-top: 1px solid #ccc; text-align: center; padding-top: 5px; margin-top: 30px; }
                .signature-text { font-size: 11px; font-weight: bold; color: #333; }
                .footer-info { font-size: 11px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="print-date">Fecha Impresión:<br><strong>' . date("d/m/Y") . '</strong></div>
                <img src="' . $logoBase64 . '" class="logo">
                <div class="header-text">
                    <h2>' . $r['nombre_liga'] . '</h2>
                    <h4>' . $r['nombre_torneo'] . '</h4>
                    <span class="badge">Documento Oficial de Competición</span>
                </div>
            </div>

            <div class="title-section">
                <div class="receipt-title">
                    <h1>RECIBO DE CAJA</h1>
                    <span>#' . $r['numero_recibo'] . '</span>
                </div>
                <div class="receipt-date">' . $fecha . '</div>
            </div>

            <div class="info-row">
                <div class="info-col">
                    <div class="label">RECIBIDO DE:</div>
                    <div class="value">' . $r['pagador'] . '</div>
                </div>
                <div class="info-col" style="text-align: right;">
                    <div class="label">TOTAL PAGADO:</div>
                    <div class="value total">' . $totalFormatted . '</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>CONCEPTO</th>
                        <th class="text-right" width="120">MONTO</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($r['detalle'] as $d) {
            $html .= '<tr>
                        <td>' . $d['concepto'] . '</td>
                        <td class="text-right">$ ' . number_format($d['monto'], 0, ',', '.') . '</td>
                    </tr>';
        }
        $html .= '
                </tbody>
            </table>

            <div class="footer">
                <div class="footer-col">
                    <p class="footer-info">Forma de Pago: <strong>' . $r['forma_pago'] . '</strong></p>
                    <p class="footer-info" style="margin-top: -10px;">Referencia: <strong>' . ($r['referencia'] ?: '-') . '</strong></p>
                    <p class="footer-info" style="margin-top: 5px;">Registrado por: ' . $r['usuario_nombre'] . ' ' . $r['usuario_apellido'] . '</p>
                </div>
                <div class="footer-col" style="float: right;">
                    <div class="signature-box">
                        <span class="signature-text">Firma Autorizada / Sello</span>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    public static function createReceiptPDF($idRecibo, $model)
    {
        $r = $model->selectRecibo($idRecibo);
        if (!$r) return false;

        $html = self::generateReceiptHTML($r);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filename = "recibo_" . $r['numero_recibo'] . ".pdf";
        $path = dirname(__DIR__, 2) . "/api/Temp/$filename";

        // Asegurar que existe Temp
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);

        file_put_contents($path, $output);
        return [
            'path' => $path,
            'filename' => $filename,
            'numero' => $r['numero_recibo']
        ];
    }
}
