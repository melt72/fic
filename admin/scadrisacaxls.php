<?php
require 'assets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;


$s = $_GET['s']; //periodo di riferimento per la liquidazione
$e = $_GET['e']; //periodo di riferimento per la liquidazione
$anno = $_GET['anno']; //anno di riferimento per la liquidazione


include 'include/functions.php';



// Connessione al database
include(__DIR__ . '/../include/configpdo.php');
$campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €', 'ZONA');
// Dati per la pagina 1

// Crea un nuovo foglio di calcolo
$spreadsheet = new Spreadsheet();

//TABELLA GENERALE

// // Popola il primo foglio
// $sheet1 = $spreadsheet->getActiveSheet();
// $sheet1->setTitle('TABELLA GENERALE');

// // Imposta la larghezza delle colonne
// $sheet1->getColumnDimension('A')->setWidth(40);
// $sheet1->getColumnDimension('B')->setWidth(15); // Aggiunto B
// $sheet1->getColumnDimension('C')->setWidth(15);
// $sheet1->getColumnDimension('D')->setWidth(20);
// $sheet1->getColumnDimension('E')->setWidth(15); // Aggiunto E
// $sheet1->getColumnDimension('F')->setWidth(20);
// $sheet1->getColumnDimension('G')->setWidth(20); // Aggiunto G
// $sheet1->getColumnDimension('H')->setWidth(20);
// $sheet1->getColumnDimension('I')->setWidth(20); // Aggiunto I
// $sheet1->getColumnDimension('J')->setWidth(30); // Aggiunto L

// $sheet1->setCellValue('A' . '1', 'Scadenziario Agenzia Risaca');
// $sheet1->getStyle('A1')->getFont()->setBold(true);
// $sheet1->setCellValue('A' . '2', 'Anno di riferimento: ');
// $sheet1->getStyle('A2')->getFont()->setBold(true);

// $sheet1->setCellValue('B' . '2', $anno);

// $sheet1->setCellValue('A' . '3', 'Periodo: ');
// $sheet1->getStyle('A3')->getFont()->setBold(true);

// $sheet1->setCellValue('B' . '3', $s . ' - ' . $e);
// // Campi della tabella
// $rowNumber = 5;
// $columnLetter = 'A';
// // foreach ($campi as $cell) {
// //     $sheet1->setCellValue($columnLetter . $rowNumber, $cell);
// //     $sheet1->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
// //     $columnLetter++;
// // }














$inizio = 0; // Variabile che identifica il primo foglio




//tutte le zone dove provv=1 sono zone con agente
try {
    $query = "SELECT * FROM `zone_roma`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati_zone_agenti   = $stmt->fetchAll();

    // Array per tenere traccia dei fogli
    $sheets = [];
    $riepilogo_zone = array();
    foreach ($dati_zone_agenti  as $row) {
        // Escludi le zone con ID 17 e 19
        if ($row['id_zona'] != '17' && $row['id_zona'] != '19') {



            //valori della tabella
            $sql = "SELECT  f.num_f, f.imp_iva, f.imp_tot, f.data_f, f.data_scadenza, c.nome  AS cliente_nome   
        FROM `fatture` f 
        INNER JOIN clienti c ON f.id_cfic = c.id_cfic 
        INNER JOIN agenti_roma ag ON ag.id_cfic=c.id_cfic
        WHERE  ag.id_zona = :id_zona
        AND f.status_invio = 'sent' 
        AND f.status = 'not_paid' 
        AND  f.ie='1'
        AND YEAR(f.data_f) = '$anno' 
        AND f.data_scadenza BETWEEN '$s' AND '$e'
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute(['id_zona' => $row['id_zona']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);



            if (count($result) > 0) {

                if ($inizio == 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle($row['nome_zona']);
                    $inizio = 1;
                } else {
                    // Crea un nuovo foglio e assegnalo all'array
                    $sheet = $spreadsheet->createSheet();
                    $sheet->setTitle($row['nome_zona']);
                }
                $sheets[$row['id_zona']] = $sheet;


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

                $sheet->setCellValue('A' . '1', 'Zona: ');
                $sheet->setCellValue('B' . '1', $row['nome_zona']);


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
            }
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


// Scrivi il file Excel nel buffer di output
$writer = new Xlsx($spreadsheet);

// //Invio del file Excel per il download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Risaca_' . $anno . '_' . $s . '-' . $e . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer->save('php://output');
exit;
