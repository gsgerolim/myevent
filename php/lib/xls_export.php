<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generateXLSExport($eventName, $data, $filename)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Inscritos');

    if (empty($data)) {
        $sheet->setCellValue('A1', 'Nenhum dado encontrado');
    } else {
        // Cabeçalhos
        $headers = array_keys($data[0]);
        $colIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colIndex . '1', ucfirst($header));
            $colIndex++;
        }

        // Linhas de dados
        $row = 2;
        foreach ($data as $line) {
            $colIndex = 'A';
            foreach ($line as $value) {
                $sheet->setCellValue($colIndex . $row, $value);
                $colIndex++;
            }
            $row++;
        }

        // Ajusta largura automática
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    $writer = new Xlsx($spreadsheet);
    $tempFile = sys_get_temp_dir() . '/' . $filename;
    $writer->save($tempFile);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    readfile($tempFile);
    unlink($tempFile);
    exit;
}
