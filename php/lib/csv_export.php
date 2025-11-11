<?php
function generateCSVExport($eventName, $rows, $filename) {
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $output = fopen('php://output', 'w');

    if (!empty($rows)) {
        fputcsv($output, array_keys($rows[0]), ';');
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }
    } else {
        fputcsv($output, ['Nenhum inscrito encontrado'], ';');
    }

    fclose($output);
}
