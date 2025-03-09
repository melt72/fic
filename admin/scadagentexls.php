<?php
require 'assets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;


$s = $_GET['s']; //periodo di riferimento 
$e = $_GET['e']; //periodo di riferimento 
$anno = $_GET['anno']; //anno di riferimento 
$agente = $_GET['agente']; //agente di riferimento

include 'include/functions.php';



// Connessione al database
include(__DIR__ . '/../include/configpdo.php');


// Crea un nuovo foglio di calcolo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle($agente);



$sql = "SELECT  f.num_f, f.imp_iva, f.imp_tot, f.data_f, f.data_scadenza, c.nome  AS cliente_nome   
FROM `fatture` f 
INNER JOIN clienti c ON f.id_cfic = c.id_cfic 

WHERE f.sigla = :sigla
AND f.status_invio = 'sent' 
AND f.status = 'not_paid' 
AND  f.ie='1'
AND YEAR(f.data_f) = '$anno' 
AND f.data_scadenza BETWEEN '$s' AND '$e'
    ";

$stmt = $db->prepare($sql);
$stmt->execute(['sigla' => $agente]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);



if (count($result) > 0) {

    // Imposta la larghezza delle colonne
    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->getColumnDimension('B')->setWidth(15); // Aggiunto B
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(15); // Aggiunto E
    $sheet->getColumnDimension('F')->setWidth(20);
    $sheet->getColumnDimension('G')->setWidth(20); // Aggiunto G
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getColumnDimension('I')->setWidth(30); // Aggiunto I

    $sheet->setCellValue('A' . '1', 'Agente: ');
    $sheet->setCellValue('B' . '1', $agente);


    $sheet->getStyle('A1')->getFont()->setBold(true);
    $sheet->setCellValue('A' . '2', 'Anno di riferimento: ');
    $sheet->getStyle('A2')->getFont()->setBold(true);

    $sheet->setCellValue('B' . '2', $anno);

    $sheet->setCellValue('A' . '3', 'Periodo: ');
    $sheet->getStyle('A3')->getFont()->setBold(true);

    $sheet->setCellValue('B' . '3', $s . ' - ' . $e);
    // Campi della tabella
    $rowNumber = 5;
    $columnLetter = 'A';
    $campi = array('NOME', 'NF', 'DATA', 'SCADENZA', 'IMPORTO');
    foreach ($campi as $cell) {
        $sheet->setCellValue($columnLetter . $rowNumber, $cell);
        $sheet->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
        $columnLetter++;
    }
    $rowNumber = 7;

    $dati_prospetto = array(); //array per i dati del prospetto

    foreach ($result as $row) {
        $data_fattura = date('d-m-Y', strtotime($row['data_f']));
        $data_scadenza = date('d-m-Y', strtotime($row['data_scadenza']));
        $importo = arrotondaEFormatta($row['imp_tot']) . ' €';

        $dati_prospetto[] = array($row['cliente_nome'], $row['num_f'], $data_fattura, $data_scadenza, $importo);
    }
    foreach ($dati_prospetto as $row) {
        $columnLetter = 'A';
        foreach ($row as $cell) {
            switch ($columnLetter) {
                case 'A':
                    $sheet->setCellValue($columnLetter . $rowNumber, $cell); // nome
                    $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    break;
                case 'B':
                    $sheet->setCellValue($columnLetter . $rowNumber, $cell); // numero fattura
                    $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    break;
                case 'C':
                    $sheet->setCellValue($columnLetter . $rowNumber, date('d-m-Y', strtotime($cell))); // data
                    $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    break;
                case 'D':
                    $sheet->setCellValue($columnLetter . $rowNumber, date('d-m-Y', strtotime($cell))); // data
                    $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    break;
                case 'E':
                    $sheet->setCellValue($columnLetter . $rowNumber, $cell); // importo
                    $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    break;
            }
            $columnLetter++;
        }
        $rowNumber++;
    }
} else {
    $sheet->setCellValue('A1', 'Nessun dato disponibile');
}














// Scrivi il file Excel nel buffer di output
$writer = new Xlsx($spreadsheet);

// //Invio del file Excel per il download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Agente_' . $agente . '_' . $anno . '_' . $s . '-' . $e . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer->save('php://output');
exit;
