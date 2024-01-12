<?php
//configurazione di alcuni parametri
$mailcommercialista = 'meltit72@gmail.com';


// parametri di configurazione
$maildiservizio = 'info@win-service.biz';


// funzione per generare una password casuale
function PasswordCasuale($lunghezza = 8, $tipo = 'all')
{
    //$caratteri_disponibili ="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    switch ($tipo) {
        case 'all':
            $caratteri_disponibili = "abcdefghijklmnpqrstuvwxyz123456789"; # code...
            break;

        default:
            $caratteri_disponibili = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; # code...
            break;
    }

    $password = "";
    for ($i = 0; $i < $lunghezza; $i++) {
        $password = $password . substr($caratteri_disponibili, rand(0, strlen($caratteri_disponibili) - 1), 1);
    }
    return $password;
}

// dati dell utente collegato
function DatiUtente($utente)
{
    include('../include/configpdo.php');
    try {
        $query = "SELECT * FROM `user` WHERE `username`=:usernameadm";
        $stmt = $db->prepare($query);
        $stmt->bindParam('usernameadm', $utente, PDO::PARAM_STR);
        $stmt->execute();
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

// foto del profilo
function fotoProfilo($id)
{
    include('../include/configpdo.php');
    if ($id != '') :
        try {
            $query = "SELECT `imm_profilo` FROM `user` WHERE `id_user`=:userpic";
            $stmt = $db->prepare($query);
            $stmt->bindParam('userpic', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['imm_profilo'] != '') {
                echo 'img_profilo/' . $row['imm_profilo'];
            } else {
                echo 'img_profilo/default.jpg';
            }
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    else :
        echo 'img_profilo/default.jpg';
    endif;
}

function ruolo($id)
{
    if ($id == '') return;
    include('../include/configpdo.php');
    try {
        $query = "SELECT `ruolo` FROM `user` WHERE `id_user`=:userpic";
        $stmt = $db->prepare($query);
        $stmt->bindParam('userpic', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);

        switch ($row['ruolo']) {
            case 'sadmin':
                echo 'Super Amministratore';
                break;
            case 'admin':
                echo 'Amministratore';
                break;
            case 'user':
                echo 'Utente';
                break;
            case 'guest':
                echo 'Ospite';
                break;
            case 'segr':
                echo 'Segreteria';
                break;
            default:
                echo '';
                break;
        };
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//inserisce un nuovo record con il nome della tabella e i dati da inserire
function inserisci($tabella, $dati)
{

    $prep = array(); //array valori
    $campi = array(); //array valori
    foreach ($dati  as $k => $v) {
        $campi[$k] = '`' . $v['name'] . '`';
        $prep[':' . $v['name']] = $v['value'];
    }

    //correzione per il controllo del checkbox  
    if (!in_array('`privato`', $campi)) {
        array_push($campi, "`privato`");
        $prep[':privato'] = '0';
    }

    include('../../include/configpdo.php');
    try {
        $query = "INSERT INTO $tabella ( " . implode(', ', array_values($campi)) . ") VALUES (" . implode(', ', array_keys($prep)) . ")";
        $stmt = $db->prepare($query);
        foreach ($prep as $key => &$val) {
            echo $key . $val;
            $stmt->bindParam("{$key}", $val);
        }
        echo $query;
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//modifica un record con il nome della tabella e i dati da inserire e l'id del record
function modifica($tabella, $dati, $id)
{

    $prep = array(); //array valori

    $query = "UPDATE $tabella SET  ";

    foreach ($dati  as $k => $v) {
        $query .= '`' . $v['name'] . '` = :' . $v['name'] . ',';
        $prep[':' . $v['name']] = $v['value'];
    }

    //correzione per il controllo del checkbox  
    if (strpos($query, 'privato') == false) {
        $query .= ' `privato`=:privato,';
        $prep[':privato'] = '0';
    }

    $query = substr($query, 0, -1) . ' WHERE id=' . $id . ';'; // remove last , and add

    include('../../include/configpdo.php');
    try {
        $stmt = $db->prepare($query);
        foreach ($prep as $key => &$val) {
            echo $key . $val;
            $stmt->bindParam("{$key}", $val);
        }
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//elimina un record con il nome della tabella e l'id del record
function elimina($tabella, $nome, $id)
{
    include('../../include/configpdo.php');
    try {
        $query = "DELETE FROM $tabella WHERE $nome=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//lista utenti o dati singoli
function anagrafica($cl = 'all')
{
    include('../include/configpdo.php');

    switch ($cl) {
        case 'clienti':
            try {
                $query = "SELECT * FROM `anagrafica` ORDER BY id ASC ";
                $stmt = $db->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            } # code...
            break;
        case 'fornitori':
            try {
                $query = "SELECT * FROM `anagrafica` ORDER BY id ASC ";
                $stmt = $db->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;
        default:
            try {
                $query = "SELECT * FROM `anagrafica` WHERE `id`=:idcliente ";
                $stmt = $db->prepare($query);
                $stmt->bindParam('idcliente', $cl, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error : " . $e->getMessage();
            }
            break;
    }
}


// funzione per inviare una mail

function TestoMailJson($txt)
{
    $mex = '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <!-- If you delete this tag, the sky will fall on your head -->
    <meta name="viewport" content="width=device-width" />

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HelpsForYou</title>

    <style>
        * {
            margin: 0;
            padding: 0;
        }

        * {
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
        }

        img {
            max-width: 100%;
        }

        .collapse {
            margin: 0;
            padding: 0;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
        }

        a {
            color: #2BA6CB;
        }

        .btn {
            text-decoration: none;
            color: #FFF;
            background-color: #f37020;
            padding: 10px 16px;
            font-weight: bold;
            margin-right: 10px;
            text-align: center;
            cursor: pointer;
            display: inline-block;
        }

        p.callout {
            padding: 15px;
            background-color: #ECF8FF;
            margin-bottom: 15px;
        }

        .callout a {
            font-weight: bold;
            color: #2BA6CB;
        }

        table.social {
            /* 	padding:15px; */
            background-color: #ebebeb;

        }

        .social .soc-btn {
            padding: 3px 7px;
            font-size: 12px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #FFF;
            font-weight: bold;
            display: block;
            text-align: center;
        }

        a.fb {
            background-color: #3B5998 !important;
        }

        a.tw {
            background-color: #1daced !important;
        }

        a.gp {
            background-color: #DB4A39 !important;
        }

        a.ms {
            background-color: #000 !important;
        }

        .sidebar .soc-btn {
            display: block;
            width: 100%;
        }

        /* ------------------------------------- 
  HEADER 
  ------------------------------------- */
        table.head-wrap {
            width: 100%;
        }

        .header.container table td.logo {
            padding: 15px;
        }

        .header.container table td.label {
            padding: 15px;
            padding-left: 0px;
        }


        /* ------------------------------------- 
  BODY 
  ------------------------------------- */
        table.body-wrap {
            width: 100%;
        }

        /* ------------------------------------- 
  TYPOGRAPHY 
  ------------------------------------- */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            line-height: 1.1;
            margin-bottom: 15px;
            color: #000;
        }

        h1 small,
        h2 small,
        h3 small,
        h4 small,
        h5 small,
        h6 small {
            font-size: 60%;
            color: #6f6f6f;
            line-height: 0;
            text-transform: none;
        }

        h1 {
            font-weight: 200;
            font-size: 44px;
        }

        h2 {
            font-weight: 200;
            font-size: 37px;
        }

        h3 {
            font-weight: 500;
            font-size: 27px;
        }

        h4 {
            font-weight: 500;
            font-size: 23px;
        }

        h5 {
            font-weight: 900;
            font-size: 17px;
        }

        h6 {
            font-weight: 900;
            font-size: 14px;
            text-transform: uppercase;
            color: #444;
        }

        .collapse {
            margin: 0 !important;
        }

        p,
        ul {
            margin-bottom: 10px;
            font-weight: normal;
            font-size: 14px;
            line-height: 1.6;
        }

        p.lead {
            font-size: 17px;
        }

        p.last {
            margin-bottom: 0px;
        }

        ul li {
            margin-left: 5px;
            list-style-position: inside;
        }

        /* ------------------------------------- 
  SIDEBAR 
  ------------------------------------- */
        ul.sidebar {
            background: #ebebeb;
            display: block;
            list-style-type: none;
        }

        ul.sidebar li {
            display: block;
            margin: 0;
        }

        ul.sidebar li a {
            text-decoration: none;
            color: #666;
            padding: 10px 16px;
            /* 	font-weight:bold; */
            margin-right: 10px;
            /* 	text-align:center; */
            cursor: pointer;
            border-bottom: 1px solid #777777;
            border-top: 1px solid #FFFFFF;
            display: block;
            margin: 0;
        }

        ul.sidebar li a.last {
            border-bottom-width: 0px;
        }

        ul.sidebar li a h1,
        ul.sidebar li a h2,
        ul.sidebar li a h3,
        ul.sidebar li a h4,
        ul.sidebar li a h5,
        ul.sidebar li a h6,
        ul.sidebar li a p {
            margin-bottom: 0 !important;
        }





        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            /* makes it centered */
            clear: both !important;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            padding: 15px;
            max-width: 600px;
            margin: 0 auto;
            display: block;
        }



        /* Odds and ends */
        .column {
            width: 300px;
            float: left;
        }

        .column tr td {
            padding: 15px;
        }

        .column-wrap {
            padding: 0 !important;
            margin: 0 auto;
            max-width: 600px !important;
        }

        .column table {
            width: 100%;
        }

        .social .column {
            width: 280px;
            min-width: 279px;
            float: left;
        }

        /* Be sure to place a .clear element after each set of columns, just to be safe */
        .clear {
            display: block;
            clear: both;
        }


        /* ------------------------------------------- 
  PHONE
  For clients that support media queries.
  Nothing fancy. 
  -------------------------------------------- */
        @media only screen and (max-width: 600px) {

            a[class="btn"] {
                display: block !important;
                margin-bottom: 10px !important;
                background-image: none !important;
                margin-right: 0 !important;
            }

            div[class="column"] {
                width: auto !important;
                float: none !important;
            }

            table.social div[class="column"] {
                width: auto !important;
            }

        }
    </style>
</head>

<body bgcolor="#ebebeb">
    <table width="100%" border="0" cellpadding="10" cellspacing="0"
        style="background-color:#e3e3e3;border-collapse:collapse;border-collapse:collapse">
        <tbody>
            <tr>
                <td valign="middle"> <br>
                    <!-- HEADER -->
                    <table class="head-wrap container" bgcolor="#FFFFFF">
                        <tr>
                            <td width="25%"></td>
                            <td class="header container">
                                <div class="content" style="text-align: center;">
                                    <img src="https://www.win-service.biz/img/winservice-logo.png" width="300px"
                                        alt="Winservice logo" />
                                </div>
                            </td>
                            <td width="25%"></td>
                        </tr>
                    </table>
                </td>
            </tr>
    </table>
    <!-- /HEADER -->


    <!-- BODY -->
    <table class="body-wrap">
        <tr>
            <td></td>
            <td class="container" bgcolor="#FFFFFF">
                <div class="content">
                    <table>
                        <tr>
                            <td>' . $txt . '

                                <!-- social & contact -->
                                <table class="social" width="100%">
                                    <tr>
                                        <td>
                                            <!--- column 1 -->
                                            <table align="center" class="column">
                                                <tr>
                                                    <td>  <img src="https://www.win-service.biz/img/ws-icon.png" alt="Winservice logo" /></td>
                                                    <td style="font-size: 12px;">
                                                        <p> Win Service srl<br>
                                                            <span style="font-size: 10px;"> Via Stadio,
                                                                23<br>
                                                                30026 Portogruaro (VE)<br>
                                                                P.IVA: IT04784620272</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!--- column 1 -->
                                            <table align="center" class="column">
                                                <tr>
                                                    <td style="font-size: 12px;">
                                                        <p>Telefono: <br>
                                                            Email: <br>
                                                            Website:
                                                        </p>
                                                    </td>
                                                    <td style="font-size: 12px;">
                                                        <p><a href="tel:+393516653771">(+39)
                                                                351 665 3771</a><br>
                                                            <a
                                                                href="mailto:info@wis-service.biz">info@win-service.biz</a><br>
                                                            <a href="https://www.winservice.biz">Win-service.biz</a>
                                                        </p>

                                                    </td>
                                                </tr>
                                            </table>
                                            <span class="clear"></span>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /social & contact -->
                            </td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>
    </table><br>
    </td>
    </tr>
    </tbody>
    </table>
</body>

</html>
';
    return $mex;
}

//parametri email da database
function getParametriEmail()
{
    include('../include/configpdo.php');
    try {
        $query = "SELECT * FROM `config` WHERE `tipo_config`='email'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati   = $stmt->fetchAll();
        foreach ($dati as $row) {
            // creo un array con i parametri di configurazione
            $config[$row['parametro_config']] = $row['valore_config'];
        }
        return $config;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//nazioni
function getNazioni()
{
    include('../include/configpdo.php');
    try {
        $query = "SELECT * FROM `stati` ORDER BY `nome_stati` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return   $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}


//funzione per prelevare i clienti da fatture in cloud
function get_clients($page = 1)
{
    include 'config-api.php';
    //array dei nomi dei clienti
    $clienti = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $clients = $clientsAPI->listClients($firstCompanyId, null, null, null, $page, 50);
        //se ci sono clienti leggo il numero di pagine
        if ($clients->getData()) {
            $pagine = $clients['last_page']; //numero di fatture trovate
            array_push($clienti, $pagine);
        }

        //per ogni cliente prelevo i dati
        foreach ($clients->getData() as $client) {

            $id = $client->getId(); //id del cliente
            $name = $client->getName(); //nome del cliente
            $citta = $client->getAddressCity(); //città del cliente
            $provincia = $client->getAddressProvince(); //provincia del cliente
            $paese = $client->getCountry(); //paese del cliente
            $datiCliente = array(
                'id' => $id,
                'name' => $name,
                'citta' => $citta,
                'provincia' => $provincia,
                'paese' => $paese
            );
            // Aggiungi l'array datiCliente all'array clienti_totali
            $clienti_totali[] = $datiCliente;
        }
        //se il numero di pagina è minore del numero di pagine totali
        if ($page < $pagine) {
            //richiamo la funzione ricorsivamente
            $page++;
            $clienti_totali = array_merge($clienti_totali, get_clients($page));
        }
        return $clienti_totali;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

/**
 * *funzione per prelevare le fatture da fatture in cloud
 *  */
function get_fatture($page = 1, $data_inizio = null)
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        //Se date l'inizio non è nulla allora prelevo le fatture in base alla data
        if ($data_inizio != null) {
            $q = "date >= " . $data_inizio;
            $issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, $page, 50, null, null, $q);
        } else {
            //altrimenti prelevo tutte le fatture
            $issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, $page, 50);
        }

        //se ci sono fatture leggo il numero di pagine
        if ($issuedEInvoices->getData()) {
            $pagine = $issuedEInvoices['last_page']; //numero di fatture trovate
            array_push($fatture, $pagine);
        }

        //per ogni fattura prelevo i dati
        foreach ($issuedEInvoices->getData() as $issuedEInvoice) {

            $id = $issuedEInvoice->getId(); //id della fattura
            $id_cliente = $issuedEInvoice->getEntity()->getId();    //id cliente
            $numero = $issuedEInvoice->getNumber(); //numero della fattura
            $imp_netto = $issuedEInvoice->getAmountNet(); //importo netto
            $iva = $issuedEInvoice->getAmountVat(); //iva
            $imp_tot = $issuedEInvoice->getAmountGross(); //importo totale
            $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

            $data = $issuedEInvoice->getDate(); //data della fattura
            //la data in formato aaaa-mm-gg
            $data = $data->format('Y-m-d');
            $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura

            //la data in formato aaaa-mm-gg
            $data_scadenza = $data_scadenza->format('Y-m-d');

            $datiFattura = array(
                'id' => $id,
                'id_cliente' => $id_cliente,
                'numero' => $numero,
                'imp_netto' => $imp_netto,
                'iva' => $iva,
                'imp_tot' => $imp_tot,
                'status' => $status,
                'data' => $data,
                'data_scadenza' => $data_scadenza
            );
            // Aggiungi l'array datiFattura all'array fatture_totali
            $fatture_totali[] = $datiFattura;
        }
        //se il 
        if ($page < $pagine) {
            //richiamo la funzione ricorsivamente
            $page++;
            $fatture_totali = array_merge($fatture_totali, get_fatture($page, $data_inizio));
        }
        return $fatture_totali;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

/**
 * *funzione per prelevare la fattura singola da fatture in cloud
 *  */
function get_fattura($id_doc)
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $id_doc);
        $issuedEInvoice = $issuedEInvoices->getData();
        //Prelevo i dati della fattura
        $id = $issuedEInvoice->getId(); //id della fattura
        $id_cliente = $issuedEInvoice->getEntity()->getId();    //id cliente
        $numero = $issuedEInvoice->getNumber(); //numero della fattura
        $imp_netto = $issuedEInvoice->getAmountNet(); //importo netto
        $iva = $issuedEInvoice->getAmountVat(); //iva
        $imp_tot = $issuedEInvoice->getAmountGross(); //importo totale
        $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura

        $data = $issuedEInvoice->getDate(); //data della fattura
        //la data in formato aaaa-mm-gg
        $data = $data->format('Y-m-d');
        $data_scadenza = $issuedEInvoice->getPaymentsList()[0]->getDueDate(); //data di scadenza della fattura

        //la data in formato aaaa-mm-gg
        $data_scadenza = $data_scadenza->format('Y-m-d');
        $datiFattura = array(
            'id' => $id,
            'id_cliente' => $id_cliente,
            'numero' => $numero,
            'imp_netto' => $imp_netto,
            'iva' => $iva,
            'imp_tot' => $imp_tot,
            'status' => $status,
            'data' => $data,
            'data_scadenza' => $data_scadenza
        );
        //Ritorno array con i dati della fattura
        return $datiFattura;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

/**
 * *Funzione per prelevare lo status della fattura
 *  */
function get_status($id_doc)
{
    include 'config-api.php';
    //array delle fatture
    $fatture = array();
    try {
        // Retrieve the first company id
        $companies = $userApi->listUserCompanies();

        // se il tipo è all allora prelevo tutte le fatture

        $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $id_doc);
        $issuedEInvoice = $issuedEInvoices->getData();
        //Prelevo i dati della fattura
        $status = $issuedEInvoice->getPaymentsList()[0]->getStatus(); //stato della fattura
        return $status;
    } catch (Exception $e) {
        echo 'Exception when calling the API: ', $e->getMessage(), PHP_EOL;
    }
}

/**
 * *Lista delle zone di Roma
 *  */

function get_zone($citta = '1')
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `zone_roma` WHERE id_citta='$citta' ORDER BY `nome_zona` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return   $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione per il nome della zona partendo dal suo id
function get_nome_zona($id_zona)
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `zone_roma` WHERE id_zona='$id_zona'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['nome_zona'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Lista dei clienti divisi per zone
 *  */

function get_clienti_zone($zona = '1')
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `agenti_roma` INNER JOIN clienti ON agenti_roma.id_cfic=clienti.id_cfic WHERE agenti_roma.id_zona='$zona' ORDER BY `nome` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return   $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funzione per vedere se un cliente è associato a una zona e nome della zona
 *  */

function cliente_associato($id_cliente)
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `agenti_roma` INNER JOIN zone_roma ON agenti_roma.id_zona=zone_roma.id_zona WHERE id_cfic='$id_cliente'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        //se il cliente è associato ritorno true
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['nome_zona'];
        } else {
            return '--';
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Lista dei clienti totali
 */

function get_clienti_totali($filtro = 'all')
{
    $clienti = array();
    require __DIR__ . '/../../include/configpdo.php';

    switch ($filtro) {
        case 'all':
            $query = "SELECT * FROM `clienti` ORDER BY `nome` ASC";
            break;

        default:
            $query = "SELECT * FROM `clienti` WHERE `provincia`='$filtro' ORDER BY `nome` ASC";
            break;
    }

    try {

        $stmt = $db->prepare($query);
        $stmt->execute();
        $dati = $stmt->fetchAll();
        foreach ($dati as $row) {
            // creo un array con i parametri di configurazione
            $clienti[$row['id_cfic']]['id'] = $row['id_cfic'];
            $clienti[$row['id_cfic']]['nome'] = $row['nome'];
            $clienti[$row['id_cfic']]['citta'] = $row['citta'];
            $clienti[$row['id_cfic']]['provincia'] = $row['provincia'];
            $clienti[$row['id_cfic']]['paese'] = $row['paese'];
            // controllo se il cliente è associato ad una zona
            $status = cliente_associato($row['id_cfic']);
            if ($status != '--') {
                $clienti[$row['id_cfic']]['associato'] = $status;
            } else {
                $clienti[$row['id_cfic']]['associato'] = '--';
            }
        }
        return $clienti;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Lista degli agenti totali
 */

function get_agenti_totali()
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `agenti` ORDER BY `nome_agente` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//funzione per i dati del singolo agente
function get_agente($id_agente)
{
    require __DIR__ . '/../../include/configpdo.php';
    try {
        $query = "SELECT * FROM `agenti` WHERE `id`=:id_agente";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Lista delle fatture per un determinato anno
function get_fatture_anno($year = 'all')
{
    require __DIR__ . '/../../include/configpdo.php';
    $query = "SELECT nome, fatture.id AS id_fatt, sigla, num_f, imp_netto, imp_iva, imp_tot, data_f, data_scadenza, status, id_liquidazione FROM fatture INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic ";

    switch ($year) {
        case 'all':
            $query .= "ORDER BY data_f DESC";
            break;

        case 'recenti':
            $query .= "WHERE YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM fatture) ORDER BY data_f DESC";
            break;

        default:
            $query .= "WHERE YEAR(data_f) = :i ORDER BY data_f DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam('i', $year, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
    }

    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}


// lista delle fatture di un agente
function get_fatture_agente($id_agente, $year = 'all')
{
    require __DIR__ . '/../../include/configpdo.php';

    // Parte comune della query
    $commonQuery = "SELECT nome, fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione` FROM `fatture` 
                    INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic 
                    INNER JOIN agenti ON fatture.sigla=agenti.sigla 
                    WHERE agenti.id=:id_agente";

    switch ($year) {
        case 'all':
            $query = $commonQuery . " ORDER BY `data_f` DESC";
            break;

        case 'recenti':
            $query = $commonQuery . " AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) ORDER BY `data_f` DESC";
            break;

        default:
            $query = $commonQuery . " AND YEAR(data_f)=:year ORDER BY `data_f` DESC";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);

    if ($year !== 'all' && $year !== 'recenti') {
        $stmt->bindParam(':year', $year, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}


function get_fatture_zona($id_zona, $year = 'all')
{
    require __DIR__ . '/../../include/configpdo.php';
    switch ($year) {
        case 'all':
            $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`  FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente ORDER BY `data_f` DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;

        case 'recenti':
            $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`  FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE fatture.sigla='RSC' AND agenti_roma.id_zona=:id_zona AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;

        default:
            $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`  FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year' ORDER BY `data_f` DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;
    }
}
/**
 * *status della fattura
 */
function status_fattura($id_fatt)
{
    require __DIR__ . '/../../include/configpdo.php';

    try {
        $query = "SELECT * FROM `fatture` WHERE `id`=:id_fatt";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_fatt', $id_fatt, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $data_scadenza = $row['data_scadenza'];
        $status = $row['status'];
        $liquidata = $row['id_liquidazione'];
        $data_oggi = date('Y-m-d');
        $status_fattura = '';

        if ($status == '' || $status == null) {
            $status_fattura = '<span class="badge badge-pill bg-danger me-1 my-2">Errore</span>';
        }
        if ($status == 'paid') {
            $status_fattura = '<span class="badge badge-pill bg-primary me-1 my-2">Pagata</span>';

            // Verifica se l'agente è associato
            $agente_associato = getDatiAgente($row['sigla']);

            if ($liquidata == '' && $agente_associato !== '--') {
                $status_fattura = '<span class="badge badge-pill bg-primary me-1 my-2">Da liquidare</span>';
            } elseif ($liquidata != '') {
                $status_fattura = '<span class="badge badge-pill bg-success me-1 my-2">Liquidata</span>';
            }
        }
        if ($status == 'not_paid') {
            $status_fattura = '<span class="badge badge-pill bg-light me-1 my-2">Non ancora pagata</span>';
        }

        if ($status == 'not_paid' && $data_oggi > $data_scadenza) {
            $status_fattura = '<span class="badge badge-pill bg-warning me-1 my-2">Scaduta</span>';
        }

        return $status_fattura;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}



//Funziona che determina gli anni disponibili per le fatture
function getAnniFatture()
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT DISTINCT YEAR(data_f) as anno FROM `fatture` ORDER BY `anno` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione per determinare l'anno più recente
function getAnnoRecente()
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT MAX(YEAR(data_f)) as anno FROM `fatture`";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['anno'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//anni disponibili per i prodotti marketing
function getAnniFattureAgente($id)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT DISTINCT YEAR(data_f) as anno FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente ORDER BY `anno` ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
//anni disponibili per i Le zone
function getAnniFattureZone($id)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT DISTINCT YEAR(data_f) as anno FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE agenti_roma.id_zona=:id_zona ORDER BY `anno` ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_zona', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione per arrotondare e formattare un numero
function arrotondaEFormatta($numero)
{
    // Arrotonda il numero
    $numeroArrotondato = round($numero, 2);

    // Utilizza number_format per formattare il numero con due decimali
    // e un punto come separatore decimale
    $numeroFormattato = number_format($numeroArrotondato, 2, ',', '.');

    return $numeroFormattato;
}

/**
 * *statistiche agente
 */

/**
 * *Fatturato dell'agente
 */
function getFatturatoTotAgente($id_agente, $year = 'all')
{
    include(__DIR__ . '/../../include/configpdo.php');
    switch ($year) {
        case 'all':
            $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);  // Aggiunto i due punti
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'recenti':
            $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);  // Aggiunto i due punti
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        default:
            $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year'";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);  // Aggiunto i due punti
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;
    }
}

function getFatturatoTotZona($id_zona = 0, $year = 'all')
{
    include(__DIR__ . '/../../include/configpdo.php');
    switch ($year) {
        case 'all':

            if ($id_zona == 0) {
                $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE fatture.sigla='RSC'";
            } else {
                $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE fatture.sigla='RSC' AND agenti_roma.id_zona='$id_zona'";
            }
            $stmt = $db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'recenti':
            $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        default:
            $query = "SELECT SUM(imp_tot) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;
    }
}
//Fatturato netto dell'agente
function getFatturatoNettoAgente($id_agente, $tipo = 'all', $year = 'all')
{
    include(__DIR__ . '/../../include/configpdo.php');
    switch ($tipo) {
        case 'all':
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'recenti':
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'incassato': //fatturato incassato anno corrente
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'incassato_anno': //fatturato incassato anno selezionato
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'da_incassare': //fatturato da incassare anno corrente
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'da_incassare_anno': //fatturato da incassare anno selezionato
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        default:
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;
    }
}
function getFatturatoNettoZona($id_zona = 0, $tipo = 'all', $year = 'all')
{
    include(__DIR__ . '/../../include/configpdo.php');
    switch ($tipo) {
        case 'all':
            if ($id_zona == 0) {
                $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE fatture.sigla='RSC'";
            } else {
                $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic WHERE fatture.sigla='RSC' AND agenti_roma.id_zona='$id_zona'";
            }
            $stmt = $db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'recenti':
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'incassato': //fatturato incassato anno corrente
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'incassato_anno': //fatturato incassato anno selezionato
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'da_incassare': //fatturato da incassare anno corrente
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        case 'da_incassare_anno': //fatturato da incassare anno selezionato
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;

        default:
            $query = "SELECT SUM(imp_netto) AS fatturato FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fatturato'];
            break;
    }
}
//Funzione per la provvigione dell'agente
function getProvvigioneAgente($id_agente, $tipo = 'all', $year = 'all')
{
    include(__DIR__ . '/../../include/configpdo.php');
    switch ($tipo) {
        case 'all':
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return $provvigione;
            break;

        case 'totale_liquidata': //provvigione liquidata totale
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND id_liquidazione!=''";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return arrotondaEFormatta($provvigione);
            break;

        case 'totale_da_liquidare': //provvigione da liquidare totale
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND (id_liquidazione IS NULL OR id_liquidazione = '')";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return arrotondaEFormatta($provvigione);

            break;


        case 'recenti':
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`)";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return $provvigione;
            break;

        case 'liquidata': //fatturato incassato anno corrente
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $provvigione = $row['provvigione'];
            return $provvigione;
            break;

        case 'liquidata_anno': //fatturato incassato anno selezionato
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return $provvigione;
            break;

        case 'da_liquidare': //fatturato da incassare anno corrente
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`) AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return arrotondaEFormatta($provvigione);
            break;

        case 'da_liquidare_anno': //fatturato da incassare anno selezionato
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = '$year' AND status='not_paid'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return $provvigione;
            break;

        default:
            $query = "SELECT SUM(imp_netto*provv_percent/100) AS provvigione FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year'";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $provvigione = $row['provvigione'];
            return $provvigione;
            break;
    }
}


//funzione per la lista delle liquidazioni di un agente
function getLiquidazioniAgente($id_agente)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT liquidazioni.id as id, data,importo,pagamento,note FROM `liquidazioni` INNER JOIN agenti ON liquidazioni.sigla=agenti.sigla WHERE agenti.id=:id_agente ORDER BY `data` DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function getLiquidazioniZona($id_zona)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT * FROM `liquidazioni` WHERE sigla='RSC' AND zona=:id_zona ORDER BY `data` DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//funzione per selezionare le fatture da liquidare di un agente
function getFattureDaLiquidareAgente($id_agente)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT *, (`imp_netto` * `provv_percent` / 100) AS provvigione, fatture.id AS id_fatt FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND `status`='paid' AND (id_liquidazione IS NULL OR id_liquidazione = '') ORDER BY `data_f` DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
function getFattureDaLiquidareZona($id_zona)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT *, SUM(`imp_netto` * 16 / 100) AS provvigione, fatture.id AS id_fatt FROM `fatture` INNER JOIN agenti_roma ON fatture.id_cfic=agenti_roma.id_cfic WHERE agenti_roma.id_zona=:id_zona AND `status`='paid' AND (id_liquidazione IS NULL OR id_liquidazione = '') ORDER BY `data_f` DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
//Funzione che calcola il totale della  della liquidazione per un agente
function getTotaleLiquidazioneAgente($id_agente)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT SUM(`imp_netto` * `provv_percent` / 100) AS totale FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND `status`='paid' AND (id_liquidazione IS NULL OR id_liquidazione = '')";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['totale'] == null) {
            return 0;
        } else {
            return $row['totale'];
        }
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funziona che calcola il totale della  della liquidazione per tutte le zone
 */
function getTotaleLiquidazioneZoneRoma()
{
    //Inizializzo un array complessivo
    $array = array();

    include(__DIR__ . '/../../include/configpdo.php');
    $zone = get_zone(); //$zone è un array con tutte le zone
    $totale_complessivo = 0;
    $totale_a = 0;
    $totale_b = 0;
    foreach ($zone as $zona) {
        $nome_zona = $zona['nome_zona'];
        $id_zona = $zona['id_zona'];
        $totale = 0; //totale della liquidazione
        $a = 0; //totale della liquidazione per l'agente 
        $b = 0; //totale della liquidazione per agenzia
        try {
            $query = "SELECT (`imp_netto` * 16 / 100) AS totale, provv_percent AS tipo FROM `fatture` INNER JOIN agenti_roma ON fatture.id_cfic=agenti_roma.id_cfic WHERE agenti_roma.id_zona=:id_zona AND `status`='paid' AND (id_liquidazione IS NULL OR id_liquidazione = '')";
            $stmt = $db->prepare($query);
            $stmt->bindParam('id_zona', $id_zona, PDO::PARAM_INT);
            $stmt->execute();
            $dati =  $stmt->fetchAll();
            foreach ($dati as $row) {
                switch ($row['tipo']) { //tipo di provvigione 1=50% agente 2=100% agenzia 3=caso particolare di roma (50% agenzia e il restante ripartito tra le altre zone)
                    case '1':
                        $a += $row['totale'] / 2;
                        $b += $row['totale'] / 2;
                        break;
                    case '2':
                        $a += 0;
                        $b += $row['totale'];
                        break;
                    case '3': //caso particolare di roma (50% agenzia e il restante ripartito tra le altre zone)
                        $a += $row['totale'] / 2;
                        $b += $row['totale'] / 2;
                        break;
                }
                $totale += $row['totale'];
            }
            $totale_complessivo += $totale;
            $totale_a += $a;
            $totale_b += $b;
            $array[$id_zona] = array(
                'nome' => $nome_zona, //nome della zona
                'totale' => $totale,
                'a' => $a,
                'b' => $b
            );
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }
    // $array['totale_complessivo'] = array(
    //     'totale' => $totale_complessivo,
    //     'a' => $totale_a,
    //     'b' => $totale_b
    // );
    return $array;
}

/**
 * *Funzione che calcola il totale della  della liquidazione per una zona
 */
function getTotaleLiquidazioneZona($id_zona)
{
    include(__DIR__ . '/../../include/configpdo.php');
    $totale = 0;
    $a = 0;
    $b = 0;
    try {
        $query = "SELECT (`imp_netto` * 16 / 100) AS totale, provv_percent AS tipo FROM `fatture` INNER JOIN agenti_roma ON fatture.id_cfic=agenti_roma.id_cfic WHERE agenti_roma.id_zona = :id_zona AND `status`='paid' AND (id_liquidazione IS NULL OR id_liquidazione = '')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_zona', $id_zona, PDO::PARAM_INT);  // Aggiunto i due punti
        $stmt->execute();
        $dati = $stmt->fetchAll();
        foreach ($dati as $row) {
            if ($row['tipo'] == 1) {
                $a += $row['totale'] / 2;
                $b += $row['totale'] / 2;
            } else {
                $a += 0;
                $b += $row['totale'];
            }
            $totale += $row['totale'];
        }
        $array = array(
            'totale' => $totale,
            'a' => $a,
            'b' => $b
        );
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione che ricava la sigla dell'agente dal suo id
function getSiglaAgente($id_agente)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT sigla FROM `agenti` WHERE `id`=:id_agente";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['sigla'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funzione che ricava i dati agenti dalla sigla
 */
function getDatiAgente($sigla)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT * FROM `agenti` WHERE `sigla`=:sigla";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) :
            return $row['nome_agente'];
        else :
            return '--';
        endif;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funzione che scrive nel database la liquidazione
 */
function setLiquidazione($sigla, $data, $totale, $metodopagamento, $note)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "INSERT INTO `liquidazioni`(`sigla`, `data`, `importo`, `pagamento`, `note`) VALUES (:sigla,:data_liq,:totale,:metodopagamento,:note)";
        $stmt = $db->prepare($query);
        $stmt->bindParam('sigla', $sigla, PDO::PARAM_STR);
        $stmt->bindParam('data_liq', $data, PDO::PARAM_STR);
        $stmt->bindParam('totale', $totale, PDO::PARAM_STR);
        $stmt->bindParam('metodopagamento', $metodopagamento, PDO::PARAM_STR);
        $stmt->bindParam('note', $note, PDO::PARAM_STR);
        $stmt->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//  Funzione che aggiorna le fatture con l'id della liquidazione
function updateFattureLiquidazione($id_liquidazione, $id_fatture)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "UPDATE `fatture` SET `id_liquidazione`=:id_liquidazione WHERE `id`=:id_fatture";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_liquidazione', $id_liquidazione, PDO::PARAM_INT);
        $stmt->bindParam('id_fatture', $id_fatture, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione che mi ritorna il metodo di pagamento in base al numero
function getMetodoPagamento($numero)
{
    switch ($numero) {
        case '1':
            # code...
            return 'Bonifico';
            break;
        case '2':
            # code...
            return 'Assegno';
            break;
        case '3':
            # code...
            return 'Contanti';
            break;
    }
}

/**
 * *Funzione per le fatture di Roma
 */
function get_fatture_roma($year = 'all')
{
    require __DIR__ . '/../../include/configpdo.php';

    switch ($year) {
        case 'all':
            $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione` FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente ORDER BY `data_f` DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;

        case 'recenti':
            $maxYearQuery = "SELECT MAX(YEAR(data_f)) AS max_year FROM `fatture` WHERE sigla='RSC'";
            $stmtMaxYear = $db->prepare($maxYearQuery);
            $stmtMaxYear->execute();
            $maxYearResult = $stmtMaxYear->fetch(PDO::FETCH_ASSOC);
            $maxYear = $maxYearResult['max_year'];

            $query = "SELECT fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`, nome_zona FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic INNER JOIN zone_roma ON agenti_roma.id_zona=zone_roma.id_zona WHERE fatture.sigla='RSC' AND YEAR(data_f) = :maxYear";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':maxYear', $maxYear, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;

        default:
            $query = "SELECT fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione` FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f) = :year ORDER BY `data_f` DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id_agente', $id_agente, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;
    }
}

// function get_fatture_roma($year = 'all')
// {
//     require __DIR__ . '/../../include/configpdo.php';
//     switch ($year) {
//         case 'all':
//             $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`  FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente ORDER BY `data_f` DESC";
//             $stmt = $db->prepare($query);
//             $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
//             $stmt->execute();
//             return $stmt->fetchAll();
//             break;

//         case 'recenti':
//             $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`, nome_zona  FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic INNER JOIN zone_roma ON agenti_roma.id_zona=zone_roma.id_zona WHERE fatture.sigla='RSC' AND YEAR(data_f) = (SELECT MAX(YEAR(data_f)) FROM `fatture`);";
//             $stmt = $db->prepare($query);
//             $stmt->execute();
//             return $stmt->fetchAll();
//             break;

//         default:
//             $query = "SELECT  fatture.id AS id_fatt, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`  FROM `fatture` INNER JOIN agenti ON fatture.sigla=agenti.sigla WHERE agenti.id=:id_agente AND YEAR(data_f)='$year' ORDER BY `data_f` DESC";
//             $stmt = $db->prepare($query);
//             $stmt->bindParam('id_agente', $id_agente, PDO::PARAM_INT);
//             $stmt->execute();
//             return $stmt->fetchAll();
//             break;
//     }
// }

/**
 * *Anni disponibili per Roma
 */
function getAnniFattureRoma()
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT DISTINCT YEAR(data_f) as anno FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic  ORDER BY `anno` ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funzioni per le liquidazioni di Roma
 */
function getLiquidazioniRoma()
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT * FROM `liquidazioni` WHERE sigla = :sigla ORDER BY `data` DESC";
        $stmt = $db->prepare($query);
        $sigla = 'RSC';
        $stmt->bindParam(':sigla', $sigla, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}


/**
 * *Funzioni per l'analisi dei dati
 */

//Funzione per determinare il totale, tutta l'incassato e totale da incassare
function getTotalFromQuery($db, $query)
{
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['totale'];
}

function analisiTotale($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        // Totale
        $queryTotale = "SELECT SUM(imp_netto) AS totale FROM `fatture` WHERE YEAR(data_f)=:anno";
        $totale = getTotalFromQuery($db, $queryTotale);

        // Incassato
        $queryIncassato = "SELECT SUM(imp_netto) AS totale FROM `fatture` WHERE YEAR(data_f)=:anno AND status='paid'";
        $incassato = getTotalFromQuery($db, $queryIncassato);

        // Da incassare
        $da_incassare = $totale - $incassato;

        // Non pagato scaduto
        $queryNonPagatoScaduto = "SELECT SUM(imp_netto) AS totale FROM `fatture` WHERE YEAR(data_f)=:anno AND status='not_paid' AND data_scadenza < CURDATE()";
        $non_pagato_scaduto = getTotalFromQuery($db, $queryNonPagatoScaduto);

        $array = array(
            'totale' => $totale,
            'incassato' => $incassato,
            'da_incassare' => $da_incassare,
            'non_pagato_scaduto' => $non_pagato_scaduto
        );
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}


/**
 * *Funzione per determinare l'imponibile totale per ciascuno dei 12 mesi di un certo anno .
 */
function analisiImponibile($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $array = array();
        for ($i = 1; $i <= 12; $i++) {
            $query = "SELECT SUM(imp_netto) AS imponibile FROM `fatture` WHERE YEAR(data_f) = :anno AND MONTH(data_f) = :mese";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
            $stmt->bindParam(':mese', $i, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $imponibile = $row['imponibile'];
            $array[$i] = $imponibile;
        }
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}



/**
 * *Funzione per determinare l'imponibile totale per i quattro trimestri di un anno
 */
function analisiImponibileTrimestre($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $array = array();
        for ($i = 1; $i <= 4; $i++) {
            $startMonth = ($i - 1) * 3 + 1;
            $endMonth = $i * 3;

            $query = "SELECT SUM(imp_netto) AS imponibile FROM `fatture` WHERE YEAR(data_f) = :anno AND MONTH(data_f) BETWEEN :startMonth AND :endMonth";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
            $stmt->bindParam(':startMonth', $startMonth, PDO::PARAM_INT);
            $stmt->bindParam(':endMonth', $endMonth, PDO::PARAM_INT);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $imponibile = $row['imponibile'];
            $array[$i] = $imponibile;
        }
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}
