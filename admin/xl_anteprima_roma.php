<?php
require 'assets/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

$fattureJson = $_GET['fatture']; //array con id_fattura
$start = $_GET['start']; //periodo di riferimento per la liquidazione
$end = $_GET['end']; //periodo di riferimento per la liquidazione
$anno = $_GET['anno']; //anno di riferimento per la liquidazione
$data_start = date('d/m/Y', strtotime($start));
$data_end = date('d/m/Y', strtotime($end));
$diritto_agenzia = 0;
$diritto = 0;
// Decodifica la stringa JSON
$array_id_fattura = json_decode(urldecode($fattureJson), true); //array con id_fattura

// // Converti l'array di ID delle fatture in una stringa per l'uso nella query SQL
$id_fatture_string = implode(',', $array_id_fattura);

include 'include/functions.php';
// Connessione al database
include(__DIR__ . '/../include/configpdo.php');
$campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €', 'ZONA');
// Dati per la pagina 1

// Crea un nuovo foglio di calcolo
$spreadsheet = new Spreadsheet();

//TABELLA GENERALE

// Popola il primo foglio
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('TABELLA GENERALE');

// Imposta la larghezza delle colonne
$sheet1->getColumnDimension('A')->setWidth(40);
$sheet1->getColumnDimension('B')->setWidth(15); // Aggiunto B
$sheet1->getColumnDimension('C')->setWidth(15);
$sheet1->getColumnDimension('D')->setWidth(20);
$sheet1->getColumnDimension('E')->setWidth(15); // Aggiunto E
$sheet1->getColumnDimension('F')->setWidth(20);
$sheet1->getColumnDimension('G')->setWidth(20); // Aggiunto G
$sheet1->getColumnDimension('H')->setWidth(20);
$sheet1->getColumnDimension('I')->setWidth(20); // Aggiunto I
$sheet1->getColumnDimension('J')->setWidth(30); // Aggiunto L

$sheet1->setCellValue('A' . '1', 'Anteprima Liquidazione Agenzia Risaca');
$sheet1->getStyle('A1')->getFont()->setBold(true);
$sheet1->setCellValue('A' . '2', 'Anno di riferimento: ');
$sheet1->getStyle('A2')->getFont()->setBold(true);

$sheet1->setCellValue('B' . '2', $anno);

$sheet1->setCellValue('A' . '3', 'Periodo: ');
$sheet1->getStyle('A3')->getFont()->setBold(true);

$sheet1->setCellValue('B' . '3', $data_start . ' - ' . $data_end);
// Campi della tabella
$rowNumber = 5;
$columnLetter = 'A';
foreach ($campi as $cell) {
    $sheet1->setCellValue($columnLetter . $rowNumber, $cell);
    $sheet1->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
    $columnLetter++;
}

//valori della tabella

//complesso agenzia calcolo iniziale
try {
    $query = "SELECT
            c.nome,
            f.num_f,
            f.data_f,
            f.imp_tot,
            f.imp_netto,
            f.imp_iva,
            (f.imp_netto * 16 / 100) AS totale
            FROM
            zone_roma z
            JOIN
            agenti_roma a ON z.id_zona = a.id_zona
            JOIN
            clienti c ON a.id_cfic = c.id_cfic
            JOIN
            fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
            WHERE
            z.id_zona = '19'
            ORDER BY
            f.num_f ASC;";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();

    if (!empty($dati)) {
        $diritto_agenzia = 1; //se ci sono dati per il complesso agenzia
        $provv_totale = 0; //totale provvigioni
        //valori della tabella
        foreach ($dati as $row) {
            $provv_totale += $row['totale'];
        }
        $complesso_agenzia = $provv_totale / 2 / 10; //provvigione complessiva per il complesso agenzia
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


















// ZONE con agenti

//('NOME', 'nf', 'Data', 'Importo', 'Iva', 'Imponibile', 'Iva in €', ' ' , 'Provvigione €','ZONA');
//   a,      b,     c,       d,       e,        f,           g,       h,          i,          l

$sql = "SELECT
c.nome,    
f.num_f,   
f.data_f, 
f.imp_tot,
    f.imp_netto, 
    f.imp_iva,   
    (f.imp_netto * 16 / 100) AS totale,    
     z.nome_zona

FROM 
    zone_roma z
JOIN 
    agenti_roma a ON z.id_zona = a.id_zona
JOIN 
    clienti c ON a.id_cfic = c.id_cfic
JOIN 
    fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
ORDER BY 
    f.num_f ASC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($result);
$rowNumber = 7;

$dati_prospetto = array();
$provv_totale = 0;
foreach ($result as $row) {
    $data_fattura = date('d-m-Y', strtotime($row['data_f']));
    $importo = arrotondaEFormatta($row['imp_tot']);
    $imponibile = arrotondaEFormatta($row['imp_netto']) . ' €';
    $iva = arrotondaEFormatta($row['imp_iva']) . ' €';
    $provvigione = arrotondaEFormatta($row['totale']) . ' €';
    $zona = $row['nome_zona'];
    $dati_prospetto[] = array($row['nome'], $row['num_f'], $data_fattura, $importo, '1.22', $imponibile, $iva, '16%', $provvigione, $zona);
    $provv_totale += $row['totale'];
}



foreach ($dati_prospetto as $row) {
    $columnLetter = 'A';
    foreach ($row as $cell) {
        switch ($columnLetter) {
            case 'A':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // nome
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                break;
            case 'B':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // numero fattura
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'C':
                $sheet1->setCellValue($columnLetter . $rowNumber, date('d-m-Y', strtotime($cell))); // data
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                break;
            case 'D':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // importo
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'E':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // iva
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'F':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // imponibile
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'G':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // iva in €
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'H':
                $sheet1->setCellValue($columnLetter . $rowNumber, '16%'); // percentuale iva
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'I':
                $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                $sheet1->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'J':
                if ($cell == 'Zona 00 speciale') {
                    $sheet1->setCellValue($columnLetter . $rowNumber, 'COMPLESSO AGENZIA (00)'); // spazio
                } else {
                    $sheet1->setCellValue($columnLetter . $rowNumber, $cell); // zona
                }
                //associo un colore per ogni zona
                switch ($cell) {
                        // le zone sono: Zona 1,Zona 3,Zona 4,Zona 5,Zona 6,Zona 7,Zona 9,Zona 10,Zona 11,Zona 15,Zona 00 speciale
                    case 'Zona 1':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0'); // Grigio chiaro
                        break;
                    case 'Zona 3':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFFF'); // Azzurro chiaro
                        break;
                    case 'Zona 4':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFBFFF00'); // Verde chiaro
                        break;
                    case 'Zona 5':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFFE0'); // Giallo chiaro
                        break;
                    case 'Zona 6':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EFFFE0EE'); // Rosso chiaro
                        break;
                    case 'Zona 7':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0E0FF'); // Viola chiaro
                        break;
                    case 'Zona 9':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFE0'); // Verde chiaro
                        break;
                    case 'Zona 10':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0FF'); // Blu chiaro
                        break;
                    case 'Zona 11':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE0E0'); // Rosso chiaro
                        break;
                    case 'Zona 15':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFFF'); // Azzurro chiaro
                        break;
                    case 'Zona 00 speciale':
                        $sheet1->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3'); // Grigio
                        break;
                }
                break;
        }

        $columnLetter++;
    }
    $rowNumber++;
}

//totale provvigioni
$sheet1->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale) . ' €');
$sheet1->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet1->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');


//tutte le zone dove provv=1 sono zone con agente
try {
    $query = "SELECT * FROM `zone_roma` WHERE `provv`='1'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati_zone_agenti   = $stmt->fetchAll();

    // Array per tenere traccia dei fogli
    $sheets = [];
    $riepilogo_zone = array();
    foreach ($dati_zone_agenti  as $row) {
        // Escludi le zone con ID 17 e 19
        if ($row['id_zona'] != '17' && $row['id_zona'] != '19') {

            // Crea un nuovo foglio e assegnalo all'array
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($row['nome_zona']);
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

            $sheet->setCellValue('B' . '3', $data_start . ' - ' . $data_end);
            // Campi della tabella
            $rowNumber = 5;
            $columnLetter = 'A';
            $campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €');
            foreach ($campi as $cell) {
                $sheet->setCellValue($columnLetter . $rowNumber, $cell);
                $sheet->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
                $columnLetter++;
            }

            //valori della tabella
            $sql = "SELECT
            c.nome,
            f.num_f,
            f.data_f,
            f.imp_tot,
            f.imp_netto,
            f.imp_iva,
            (f.imp_netto * 8 / 100) AS totale,
            z.nome_zona
            FROM
            zone_roma z
            JOIN
            agenti_roma a ON z.id_zona = a.id_zona
            JOIN
            clienti c ON a.id_cfic = c.id_cfic
            JOIN
            fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
            WHERE
            z.id_zona = :id_zona
            ORDER BY
            f.num_f ASC;
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute(['id_zona' => $row['id_zona']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rowNumber = 7;

            $dati_prospetto = array(); //array per i dati del prospetto
            $provv_totale = 0; //totale provvigioni
            foreach ($result as $row) {
                $data_fattura = date('d-m-Y', strtotime($row['data_f']));
                $importo = arrotondaEFormatta($row['imp_tot']) . ' €';
                $imponibile = arrotondaEFormatta($row['imp_netto']) . ' €';
                $iva = arrotondaEFormatta($row['imp_iva']) . ' €';
                $provvigione = arrotondaEFormatta($row['totale']) . ' €';

                $dati_prospetto[] = array($row['nome'], $row['num_f'], $data_fattura, $importo, '1.22', $imponibile, $iva, '8%', $provvigione);
                $provv_totale += $row['totale'];
            }
            $riepilogo_zone[] = array($row['nome_zona'], $provv_totale + $complesso_agenzia);

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
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // importo
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            break;
                        case 'E':
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // imponibile
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;
                        case 'F':
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // iva
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            break;
                        case 'G':
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // percentuale iva
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            break;
                        case 'H':
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            break;
                        case 'I':
                            $sheet->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                            $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            break;
                    }
                    $columnLetter++;
                }
                $rowNumber++;
            }

            //totale provvigioni
            $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale) . ' €');
            $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
            $rowNumber++;
            $sheet->setCellValue('G' . $rowNumber, 'Complesso Agenzia');

            $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia) . ' €');
            $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $rowNumber++;
            $sheet->setCellValue('G' . $rowNumber, 'Totale Generale');
            $sheet->getStyle('G' . $rowNumber)->getFont()->setBold(true);

            $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia + $provv_totale) . ' €');
            $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

// complesso agenzia

$sheet = $spreadsheet->createSheet();
$sheet->setTitle('COMPLESSO AGENZIA');

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
$sheet->setCellValue('B' . '1', 'COMPLESSO AGENZIA');
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->setCellValue('A' . '2', 'Anno di riferimento: ');
$sheet->getStyle('A2')->getFont()->setBold(true);

$sheet->setCellValue('B' . '2', $anno);

$sheet->setCellValue('A' . '3', 'Periodo: ');
$sheet->getStyle('A3')->getFont()->setBold(true);
$sheet->setCellValue('B' . '3', $data_start . ' - ' . $data_end);
$rowNumber = 5;
$columnLetter = 'A';

$campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €');
foreach ($campi as $cell) {
    $sheet->setCellValue($columnLetter . $rowNumber, $cell);
    $sheet1->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
    $columnLetter++;
}

$rowNumber++;
//valori della tabella
try {
    $query = "SELECT
            c.nome,
            f.num_f,
            f.data_f,
            f.imp_tot,
            f.imp_netto,
            f.imp_iva,
            (f.imp_netto * 16 / 100) AS totale
            FROM
            zone_roma z
            JOIN
            agenti_roma a ON z.id_zona = a.id_zona
            JOIN
            clienti c ON a.id_cfic = c.id_cfic
            JOIN
            fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
            WHERE
            z.id_zona = '19'
            ORDER BY
            f.num_f ASC;";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dati   = $stmt->fetchAll();

    $rowNumber = 5;
    $columnLetter = 'A';
    $diritto_agenzia = 0;
    if (!empty($dati)) {
        $diritto_agenzia = 1; //se ci sono dati per il complesso agenzia

        //creo il prospetto
        $campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €');
        foreach ($campi as $cell) {
            $sheet->setCellValue($columnLetter . $rowNumber, $cell);
            $sheet->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
            $columnLetter++;
        }
        $dati_prospetto = array(); //array per i dati del prospetto 

        $rowNumber++;
        $rowNumber++;

        $provv_totale = 0; //totale provvigioni
        //valori della tabella
        foreach ($dati as $row) {
            $data_fattura = date('d-m-Y', strtotime($row['data_f']));
            $importo = arrotondaEFormatta($row['imp_tot']) . ' €';
            $imponibile = arrotondaEFormatta($row['imp_netto']) . ' €';
            $iva = arrotondaEFormatta($row['imp_iva']) . ' €';
            $provvigione = arrotondaEFormatta($row['totale']) . ' €';

            $dati_prospetto_complesso_agenzia[] = array($row['nome'], $row['num_f'], $data_fattura, $importo, '1.22', $imponibile, $iva, '16%', $provvigione);
            $provv_totale += $row['totale'];
        }
        $complesso_agenzia = $provv_totale / 2 / 10; //provvigione complessiva per il complesso agenzia

        //creazione dello schema

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
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // importo
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        break;
                    case 'E':
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // imponibile
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        break;
                    case 'F':
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // iva
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        break;
                    case 'G':
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // percentuale iva
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        break;
                    case 'H':
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        break;
                    case 'I':
                        $sheet->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                        $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        break;
                }
                $columnLetter++;
            }
            $rowNumber++;
        }
        //totale della provvigione

        //totale provvigioni
        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
        $sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);

        $rowNumber++;
        $rowNumber++;

        $sheet->setCellValue('H' . $rowNumber, '50% di ' . arrotondaEFormatta($provv_totale) . ' €');
        $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale / 2) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $rowNumber++;
        $sheet->setCellValue('H' . $rowNumber, '1/10 del 50%  ' . arrotondaEFormatta($provv_totale) . ' €');
        $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $rowNumber++;
        $rowNumber++;
        $conta_zone = 0;
        foreach ($dati_zone_agenti  as $row) {
            // Escludi le zone con ID 17 e 19
            if ($row['id_zona'] != '17' && $row['id_zona'] != '19') {
                $sheet->setCellValue('H' . $rowNumber, $row['nome_zona']);
                $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

                $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia) . ' €');
                $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $rowNumber++;
                $conta_zone++;
            }
        }
        $sheet->setCellValue('H' . $rowNumber, 'TOTALE AGENTI');
        $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia * $conta_zone) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
        $sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
        //ripartizione del complesso agenzia

        $rowNumber++;
        $rowNumber++;

        //riporto del 50% del totale
        $sheet->setCellValue('H' . $rowNumber, 'RISACA SRL');
        $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale / 2) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); //riporto del 50% del totale
        $sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);

        $rowNumber++;
        $conta_zone = 0;

        try {
            $query = "SELECT * FROM `zone_roma` WHERE `provv`='2'"; //zone con solo agenzia
            $stmt = $db->prepare($query);
            $stmt->execute();
            $dati   = $stmt->fetchAll();
            $n = 0;
            foreach ($dati as $row) {
                if (($row['id_zona'] != '17') && ($row['id_zona'] != '19')) {
                    $sheet->setCellValue('H' . $rowNumber, $row['nome_zona']);
                    $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

                    $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($complesso_agenzia) . ' €');
                    $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $rowNumber++;
                    $conta_zone++;
                }
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }

        $sheet->setCellValue('H' . $rowNumber, 'TOTALE RISACA SRL');
        $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);
        $totale_complesso_agenzia = ($provv_totale / 2) + ($conta_zone * $complesso_agenzia);
        $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($totale_complesso_agenzia) . ' €');
        $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); //riporto del 50% del totale
        $sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
        $sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
    } else {
        $sheet->setCellValue('A' . '1', 'NESSUN COMPLESSO AGENZIA');
    }
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}


// complesso agenzia
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('RISACA SRL');

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
$sheet->setCellValue('B' . '1', 'RISACA SRL');
$sheet->getStyle('A1')->getFont()->setBold(true);
$sheet->setCellValue('A' . '2', 'Anno di riferimento: ');
$sheet->getStyle('A2')->getFont()->setBold(true);

$sheet->setCellValue('B' . '2', $anno);

$sheet->setCellValue('A' . '3', 'Periodo: ');
$sheet->getStyle('A3')->getFont()->setBold(true);
$sheet->setCellValue('B' . '3', $data_start . ' - ' . $data_end);

$rowNumber = 5;
$columnLetter = 'A';

$campi = array('NOME', 'NF', 'DATA', 'IMPORTO', 'IVA', 'IMPONIBILE',  'IVA in €', ' ', 'PROVV €');
foreach ($campi as $cell) {
    $sheet->setCellValue($columnLetter . $rowNumber, $cell);
    $sheet->getStyle($columnLetter . $rowNumber)->getFont()->setBold(true);
    $columnLetter++;
}

//prendo tutte le fatture

$sql = "SELECT
c.nome,    
f.num_f,   
f.data_f, 
f.imp_tot,
    f.imp_netto, 
    f.imp_iva,   
    (f.imp_netto * 16 / 100) AS totale,    
     z.nome_zona,
     f.provv_percent,
     z.id_zona

FROM 
    zone_roma z
JOIN 
    agenti_roma a ON z.id_zona = a.id_zona
JOIN 
    clienti c ON a.id_cfic = c.id_cfic
JOIN 
    fatture f ON c.id_cfic = f.id_cfic AND f.id IN ($id_fatture_string)
ORDER BY 
    f.num_f ASC;
";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($result);
$rowNumber = 7;

$dati_prospetto = array(); //array per i dati del prospetto
$provv_totale = 0;
foreach ($result as $row) {
    if ($row['id_zona'] != '19') {
        $data_fattura = date('d-m-Y', strtotime($row['data_f']));
        $importo = arrotondaEFormatta($row['imp_tot']);
        $imponibile = arrotondaEFormatta($row['imp_netto']) . ' €';
        $iva = arrotondaEFormatta($row['imp_iva']) . ' €';
        if ($row['provv_percent'] == 1) {
            $provvigione = arrotondaEFormatta($row['totale'] / 2) . ' €';
            $provv_totale += $row['totale'] / 2;
        } else {
            $provvigione = arrotondaEFormatta($row['totale']) . ' €';
            $provv_totale += $row['totale'];
        }

        $tipo_di_provvigione = $row['provv_percent'] == 1 ? '8%' : '16%';
        $zona = $row['nome_zona'];
        $dati_prospetto[] = array($row['nome'], $row['num_f'], $data_fattura, $importo, '1.22', $imponibile, $iva, $tipo_di_provvigione, $provvigione, $zona);
    }
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
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // importo
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'E':
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // iva
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'F':
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // imponibile
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'G':
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // iva in €
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'H':
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // percentuale iva
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'I':
                $sheet->setCellValue($columnLetter . $rowNumber, $cell); // provvigione
                $sheet->getStyle($columnLetter . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                break;
            case 'J':
                if ($cell == 'Zona 00 speciale') {
                    $sheet->setCellValue($columnLetter . $rowNumber, 'COMPLESSO AGENZIA (00)'); // spazio
                } else {
                    $sheet->setCellValue($columnLetter . $rowNumber, $cell); // zona
                }
                //associo un colore per ogni zona
                switch ($cell) {
                        // le zone sono: Zona 1,Zona 3,Zona 4,Zona 5,Zona 6,Zona 7,Zona 9,Zona 10,Zona 11,Zona 15,Zona 00 speciale
                    case 'Zona 1':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0'); // Grigio chiaro
                        break;
                    case 'Zona 3':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFFF'); // Azzurro chiaro
                        break;
                    case 'Zona 4':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFBFFF00'); // Verde chiaro
                        break;
                    case 'Zona 5':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFFE0'); // Giallo chiaro
                        break;
                    case 'Zona 6':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EFFFE0EE'); // Rosso chiaro
                        break;
                    case 'Zona 7':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0E0FF'); // Viola chiaro
                        break;
                    case 'Zona 9':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFE0'); // Verde chiaro
                        break;
                    case 'Zona 10':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0FF'); // Blu chiaro
                        break;
                    case 'Zona 11':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFE0E0'); // Rosso chiaro
                        break;
                    case 'Zona 15':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0FFFF'); // Azzurro chiaro
                        break;
                    case 'Zona 00 speciale':
                        $sheet->getStyle($columnLetter . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3'); // Grigio
                        break;
                }
                break;
        }
        $columnLetter++;
    }
    $rowNumber++;
}
//totale provvigioni
$sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($provv_totale) . ' €');
$sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
$sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);

$rowNumber++;
$sheet->setCellValue('H' . $rowNumber, 'Complesso Agenzia');
$sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

if ($diritto_agenzia == 1) {
    $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($totale_complesso_agenzia) . ' €');
    $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
} else {
    $sheet->setCellValue('I' . $rowNumber, '0 €');
    $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

$sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); //riporto del 50% del totale
$sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
$rowNumber++;
$sheet->setCellValue('H' . $rowNumber, 'Risaca Totale generale');
$sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);
$sheet->getStyle('H' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');

$sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($totale_complesso_agenzia + $provv_totale) . ' €');
$sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); //riporto del 50% del totale
$sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
$sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
$rowNumber++;
$rowNumber++;
$rowNumber++;
$sheet->setCellValue('H' . $rowNumber, 'Risaca SRL');
$sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

$totale_risaca = $totale_complesso_agenzia + $provv_totale;

$sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($totale_risaca) . ' €');
$sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); //riporto del 50% del totale
$sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);

$rowNumber++;

$totale_zone = 0;
foreach ($riepilogo_zone as $row) {
    $sheet->setCellValue('H' . $rowNumber, $row[0]);
    $sheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);

    $sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($row[1]) . ' €');
    $sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $totale_zone += $row[1];
    $rowNumber++;
}

$sheet->setCellValue('I' . $rowNumber, arrotondaEFormatta($totale_risaca + $totale_zone) . ' €');
$sheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('I' . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFD700');
$sheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
// Scrivi il file Excel nel buffer di output
$writer = new Xlsx($spreadsheet);

// //Invio del file Excel per il download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Risaca_' . $anno . '_' . $data_start . '-' . $data_end . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer->save('php://output');
exit;
