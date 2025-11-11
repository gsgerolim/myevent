<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function generatePDFExport($eventName, $rows, $filename)
{
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);

    $logoUrl = 'http://localhost/myevent/assets/img/logo.png'; // ajuste o caminho conforme seu projeto
    $exportDate = date('d/m/Y H:i');

    // Cabeçalho + tabela com estilo
    $html = "
    <html>
    <head>
        <style>
            @page {
                margin: 80px 40px;
            }

            header {
                position: fixed;
                top: -60px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: center;
                border-bottom: 1px solid #ccc;
            }

            footer {
                position: fixed;
                bottom: -40px;
                left: 0;
                right: 0;
                height: 30px;
                font-size: 10px;
                color: #777;
                text-align: center;
                border-top: 1px solid #ccc;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #333;
            }

            .header-logo {
                height: 40px;
                vertical-align: middle;
            }

            .header-title {
                display: inline-block;
                vertical-align: middle;
                font-size: 18px;
                font-weight: bold;
                color: #444;
                margin-left: 10px;
            }

            h2 {
                text-align: center;
                margin-top: 10px;
                color: #222;
            }

            .subtitle {
                text-align: center;
                color: #666;
                font-size: 11px;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th, td {
                border: 1px solid #ccc;
                padding: 6px 8px;
                text-align: left;
            }

            th {
                background-color: #f5f5f5;
                font-weight: bold;
                color: #333;
            }

            tr:nth-child(even) {
                background-color: #fafafa;
            }
        </style>
    </head>
    <body>
        <header>
            <img src='{$logoUrl}' class='header-logo'>
            <span class='header-title'>MyEvent - Relatório de Inscritos</span>
        </header>

        <footer>
            Gerado automaticamente por MyEvent em {$exportDate} — Página <span class='pageNumber'></span> / <span class='totalPages'></span>
        </footer>

        <main>
            <h2>Inscritos no evento: <em>{$eventName}</em></h2>
            <div class='subtitle'>Exportado em {$exportDate}</div>
            <table>
                <thead><tr>";

    // Cabeçalhos
    if (!empty($rows)) {
        foreach (array_keys($rows[0]) as $col) {
            $html .= "<th>" . htmlspecialchars(ucwords(str_replace('_', ' ', $col))) . "</th>";
        }
        $html .= "</tr></thead><tbody>";

        // Linhas
        foreach ($rows as $r) {
            $html .= "<tr>";
            foreach ($r as $v) {
                $html .= "<td>" . htmlspecialchars($v ?? '-') . "</td>";
            }
            $html .= "</tr>";
        }
    } else {
        $html .= "<th>Nenhum inscrito encontrado</th></tr></thead><tbody>";
    }

    $html .= "</tbody></table>
        </main>
    </body>
    </html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Adiciona números de página
    $canvas = $dompdf->getCanvas();
    $font = $dompdf->getFontMetrics()->getFont('Arial', 'normal');
    $canvas->page_text(520, 810, "Página {PAGE_NUM}/{PAGE_COUNT}", $font, 9, [0.5, 0.5, 0.5]);

    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    echo $dompdf->output();
}
