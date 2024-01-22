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
        $query = "SELECT * FROM `zone_roma` WHERE id_citta='$citta' ORDER BY `id_zona` ASC";
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
        $query = "SELECT
        fatture.*,
        (`imp_netto` * `provv_percent` / 100) AS provvigione,
        fatture.id AS id_fatt,
        clienti.nome AS nome_cliente
    FROM
        `fatture`
    INNER JOIN
        agenti ON fatture.sigla = agenti.sigla
    INNER JOIN
        clienti ON fatture.id_cfic = clienti.id_cfic
    WHERE
        agenti.id = :id_agente
        AND `status` = 'paid'
        AND (id_liquidazione IS NULL OR id_liquidazione = '')
    ORDER BY
        `data_f` DESC;
    ";
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

//funzione Che calcola il totale della liquidazione di un agente in base al numero delle fatture
function importoliquidzione($id_fattura)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT (`imp_netto` * `provv_percent` / 100) AS totale FROM `fatture`  WHERE id=:id_fattura";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_fattura', $id_fattura, PDO::PARAM_INT);
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
function importoliquidzione_roma($id_fattura)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT (`imp_netto` * `provv_percent` / 100) AS totale FROM `fatture`  WHERE id=:id_fattura";
        $stmt = $db->prepare($query);
        $stmt->bindParam('id_fattura', $id_fattura, PDO::PARAM_INT);
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

            $query = "SELECT fatture.id AS id_fatt,nome, `num_f`,`imp_netto`,`imp_iva`,`imp_tot`,`data_f`,`data_scadenza`,`provv_percent`,`id_liquidazione`, nome_zona, zone_roma.id_zona FROM `fatture` INNER JOIN clienti ON fatture.id_cfic=clienti.id_cfic INNER JOIN agenti_roma ON clienti.id_cfic=agenti_roma.id_cfic INNER JOIN zone_roma ON agenti_roma.id_zona=zone_roma.id_zona WHERE fatture.sigla='RSC' AND YEAR(data_f) = :maxYear";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':maxYear', $maxYear, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            break;

        case 'liquidazione':
            $query = "SELECT 
                fatture.id AS id_fatt,
                nome,
                num_f,
                imp_netto,
                imp_iva,
                imp_tot,
                data_f,
                data_scadenza,
                provv_percent,
                id_liquidazione,
                nome_zona,
                zone_roma.id_zona
            FROM 
                fatture
            INNER JOIN 
                clienti ON fatture.id_cfic = clienti.id_cfic
            INNER JOIN 
                agenti_roma ON clienti.id_cfic = agenti_roma.id_cfic
            INNER JOIN 
                zone_roma ON agenti_roma.id_zona = zone_roma.id_zona
            WHERE 
                fatture.sigla = 'RSC' AND status='paid' AND id_liquidazione IS NULL
            ";
            $stmt = $db->prepare($query);
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

//Funzione per impostare una determinata percentuale per una fattura a cui manca
function setPercentualeFattura($id_fattura, $id_zona)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        //Ricavo la percentuale della zona
        $query = "SELECT provv FROM `zone_roma` WHERE id_zona=:id_zona";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_zona', $id_zona, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $provv = $row['provv'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }

    try {
        //Imposto la percentuale della fattura
        $query = "UPDATE `fatture` SET `provv_percent`=:provv WHERE `id`=:id_fattura";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':provv', $provv, PDO::PARAM_INT);
        $stmt->bindParam(':id_fattura', $id_fattura, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    echo $provv;
}

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
function getTotalFromQuery($db, $anno, $query)
{
    $stmt = $db->prepare($query);
    $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
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
        $totale = getTotalFromQuery($db,  $anno, $queryTotale);

        // Incassato
        $queryIncassato = "SELECT SUM(imp_netto) AS totale FROM `fatture` WHERE YEAR(data_f)=:anno AND status='paid'";
        $incassato = getTotalFromQuery($db, $anno,  $queryIncassato);

        // Da incassare
        $da_incassare = $totale - $incassato;

        // Non pagato scaduto
        $queryNonPagatoScaduto = "SELECT SUM(imp_netto) AS totale FROM `fatture` WHERE YEAR(data_f)=:anno AND status='not_paid' AND data_scadenza < CURDATE()";
        $non_pagato_scaduto = getTotalFromQuery($db, $anno,  $queryNonPagatoScaduto);

        $array = array(
            'totale' => arrotondaEFormatta($totale),
            'incassato' => arrotondaEFormatta($incassato),
            'da_incassare' => arrotondaEFormatta($da_incassare),
            'non_pagato_scaduto' => arrotondaEFormatta($non_pagato_scaduto)
        );
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione per determinare l'imponibile totale per ciascuno dei 12 mesi di un certo anno .

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
            if ($imponibile == null) {
                $imponibile = '0';
            }
            $array[] = $imponibile; // Modifica qui per pushare il valore nell'array
        }
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare l'imponibile netto per un determinato anno .
function analisiImponibileNetto($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT SUM(imp_netto) AS imponibile FROM `fatture` WHERE YEAR(data_f) = :anno";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $imponibile = $row['imponibile'];
        if ($imponibile == null) {
            $imponibile = '0';
        }
        return $imponibile;
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
            if ($imponibile == null) {
                $array[$i] = 'N/A';
            } else {
                $array[$i] = arrotondaEFormatta($imponibile);
            }
        }
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funzione per determinare imponibile netto per ciascun paese per un determinato anno .
function analisiImponibilePerPaese($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        c.paese,
        SUM(f.imp_netto) AS somma_imponibile,
        (SUM(f.imp_netto) / (SELECT SUM(imp_netto) FROM fatture WHERE YEAR(data_f) =:anno)) * 100 AS percentuale
    FROM
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
    WHERE
        YEAR(f.data_f) = :anno
    GROUP BY
        c.paese
    ORDER BY
        somma_imponibile DESC;
    ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


/**
 * *Funzione per determinare Il numero di bottiglie vendute per un determinato anno
 */

function analisiBottiglie($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT SUM(prodotti.qta) AS totale_qta FROM `prodotti` 
                   INNER JOIN fatture ON prodotti.id_ffic = fatture.id_ffic 
                   WHERE YEAR(fatture.data_f) = :anno";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result['totale_qta'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie è venduto per ciascun prodotto di un determinato anno .
 */
function analisiBottigliePerProdotto($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        nome_prodotto,
        SUM(p.qta) AS quantita_prodotto
    FROM
        fatture f INNER JOIN prodotti p ON f.id_ffic=p.id_ffic
    JOIN
        lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        YEAR(f.data_f) = :anno
    GROUP BY
        YEAR(f.data_f),
        nome_prodotto";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie Venduto in ciascuna provincia.
 */
function analisiBottigliePerProvincia($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    $tot = analisiBottiglie($anno);
    try {
        $query = "SELECT
        nome_provincia,
        SUM(p.qta) AS quantita_prodotto,
        SUM(p.qta)/$tot*100 AS percentuale
    FROM
        fatture f INNER JOIN prodotti p ON f.id_ffic=p.id_ffic
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
     JOIN 
     province pr ON c.provincia=pr.pv   
    WHERE
        YEAR(f.data_f) = :anno AND pr.stato='IT'
    GROUP BY
        YEAR(f.data_f),
         nome_provincia
         ORDER by quantita_prodotto DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funziona per determinare l imponibile venduto per ciascuna provincia in un determinato anno
function analisiImponibilePerProvincia($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        nome_provincia,
        SUM(f.imp_netto) AS imponibile,
        SUM(f.imp_netto)/(SELECT SUM(imp_netto) FROM fatture WHERE YEAR(data_f) =:anno)*100 AS percentuale
    FROM
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
     JOIN 
     province pr ON c.provincia=pr.pv   
    WHERE
        YEAR(f.data_f) = :anno AND pr.stato='IT'
    GROUP BY
        nome_provincia
    ORDER BY
        imponibile DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
/**
 * *Funzione per determinare Il numero di bottiglie Venduto in ciascuna macro regione.
 */
function analisiBottigliePerMacroRegione($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        $query = "SELECT
            pr.nome_macro AS regione,
            SUM(p.qta) AS totale_bottiglie
        FROM
            fatture f
        JOIN
            prodotti p ON f.id_ffic = p.id_ffic
        JOIN
            clienti c ON f.id_cfic = c.id_cfic
        JOIN 
            province pr ON c.provincia = pr.pv
        WHERE
            YEAR(f.data_f) = :anno AND pr.stato='IT'  
        GROUP BY
            regione";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funzione per determinare i migliori clienti per un determinato anno .
function analisiMiglioriClienti($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        c.nome AS nome_cliente,
        c.provincia,
        SUM(f.imp_netto) AS imponibile
    FROM
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
    WHERE
        YEAR(f.data_f) = :anno
    GROUP BY
        c.id_cfic
    ORDER BY
        imponibile DESC
    LIMIT 15";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funzione per determinare importo netto per Una macro regione .
function analisiImportoPerMacroRegione($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    $tot = analisiImponibileNetto($anno);
    try {
        $query = "SELECT
            pr.nome_macro AS regione,
            SUM(f.imp_netto) AS totale_importo,
            SUM(f.imp_netto)/$tot*100 AS percentuale_importo
        FROM
            fatture f
        JOIN
            clienti c ON f.id_cfic = c.id_cfic
        JOIN 
            province pr ON c.provincia = pr.pv
        WHERE
            YEAR(f.data_f) = :anno AND pr.stato='IT'  
        GROUP BY
            regione";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie Venduto in ciascuna citta.
 */
function analisiBottigliePerCitta($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    $tot = analisiBottiglie($anno);
    try {
        $query = "SELECT
        citta,
        SUM(p.qta) AS quantita_prodotto,
        SUM(p.qta)/$tot*100 AS percentuale
    FROM
        fatture f INNER JOIN prodotti p ON f.id_ffic=p.id_ffic
    JOIN
        clienti c ON f.id_cfic = c.id_cfic   
    WHERE
        YEAR(f.data_f) = :anno
    GROUP BY
        YEAR(f.data_f),
         citta
         ORDER by quantita_prodotto DESC;";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il tipo di bottiglia 75 cm o 150 cl ..
 */

function analisiBottigliePerTipo($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        // Ottieni il totale delle bottiglie per l'anno specificato
        $tot = analisiBottiglie($anno);

        // Query per ottenere la quantità totale di bottiglie da 75cl e 150cl
        $query = "SELECT
            SUM(CASE WHEN lp.nome_prodotto LIKE '%75CL%' THEN p.qta ELSE 0 END) AS quantita_75cl,
            SUM(CASE WHEN lp.nome_prodotto LIKE '%150CL%' THEN p.qta ELSE 0 END) AS quantita_150cl,
            (SUM(CASE WHEN lp.nome_prodotto LIKE '%75CL%' THEN p.qta ELSE 0 END) / $tot) * 100 AS percentuale_75cl,
            (SUM(CASE WHEN lp.nome_prodotto LIKE '%150CL%' THEN p.qta ELSE 0 END) / $tot) * 100 AS percentuale_150cl
        FROM
            fatture f
            INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
            JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
        WHERE
            YEAR(f.data_f) = :anno";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Restituisci i risultati
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie Per ciascuna varietà di vino
 */
function analisiBottigliePerVarieta($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');

    // Ottieni il totale delle bottiglie per l'anno specificato
    $tot = analisiBottiglie($anno);
    try {
        $query = "SELECT
        SUM(CASE WHEN lp.nome_prodotto LIKE '%CABERNET%' THEN p.qta ELSE 0 END) AS cabernet,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%CHARDONNAY%' THEN p.qta ELSE 0 END) AS chardonay,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%FILOROSSO%' THEN p.qta ELSE 0 END) AS filorosso ,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%FRIULANO%' THEN p.qta ELSE 0 END) AS friulano,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%MALVASIA%' THEN p.qta ELSE 0 END) AS malvasia,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%GRIGIO%' THEN p.qta ELSE 0 END) AS grigio,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%NERO%' THEN p.qta ELSE 0 END) AS nero,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%RIBOLLA%' THEN p.qta ELSE 0 END) AS ribolla,
        SUM(CASE WHEN lp.nome_prodotto LIKE '%SAUVIGNON%' THEN p.qta ELSE 0 END) AS sauvignon,
                    
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%CABERNET%' THEN p.qta ELSE 0 END) / $tot) * 100 AS cabernet_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%CHARDONNAY%' THEN p.qta ELSE 0 END) / $tot) * 100 AS chardonay_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%FILOROSSO%' THEN p.qta ELSE 0 END) / $tot) * 100 AS filorosso_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%FRIULANO%' THEN p.qta ELSE 0 END) / $tot) * 100 AS friulano_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%MALVASIA%' THEN p.qta ELSE 0 END) / $tot) * 100 AS malvasia_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%GRIGIO%' THEN p.qta ELSE 0 END) / $tot) * 100 AS grigio_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%NERO%' THEN p.qta ELSE 0 END) / $tot) * 100 AS nero_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%RIBOLLA%' THEN p.qta ELSE 0 END) / $tot) * 100 AS ribolla_percent,
        (SUM(CASE WHEN lp.nome_prodotto LIKE '%SAUVIGNON%' THEN p.qta ELSE 0 END) / $tot) * 100 AS sauvignon_percent
    FROM
        fatture f
        INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
        JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        YEAR(f.data_f) = :anno";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci la somma delle quantità
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie Per mese e varietà specifica di vino
 */
function analisiBottigliePerMeseVarieta($anno, $varieta)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $array = array();
        for ($i = 1; $i <= 12; $i++) {
            $query = "SELECT
            SUM(p.qta) AS quantita_prodotto
        FROM
            fatture f
            INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
            JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
        WHERE
            YEAR(f.data_f) = :anno AND MONTH(f.data_f) = :mese AND lp.nome_prodotto LIKE :varieta";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
            $stmt->bindParam(':mese', $i, PDO::PARAM_INT);
            $varietaLike = '%' . $varieta . '%'; // Aggiungi il carattere jolly % prima e dopo la varietà
            $stmt->bindParam(':varieta', $varietaLike, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $imponibile = $row['quantita_prodotto'];
            if ($imponibile === null) {
                $imponibile = '0';
            }
            $array[] = $imponibile;
        }
        return $array;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

/**
 * *Funzione per determinare Il numero di bottiglie Per varietà E id_prod .
 */
function analisiBottigliePerVarietaId($anno)
{
    include(__DIR__ . '/../../include/configpdo.php');

    $array = array('cabernet', 'chardonnay', 'filorosso', 'friulano', 'malvasia', 'pinot grigio', 'pinot nero', 'ribolla', 'sauvignon');
    $resultArray = array();

    foreach ($array as $varieta) {
        try {
            $query = "SELECT
                lp.nome_prodotto,
                SUM(p.qta) AS quantita_prodotto
            FROM
                fatture f
                INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
                INNER  JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
            WHERE
                YEAR(f.data_f) = :anno AND lp.varieta= :varieta
            GROUP BY
                lp.nome_prodotto";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
            $stmt->bindParam(':varieta', $varieta, PDO::PARAM_STR);
            $stmt->execute();

            $prodottiVarieta = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nomeProdotto = $row['nome_prodotto'];
                $quantita = $row['quantita_prodotto'];
                $prodottiVarieta[] = array('nome' => $nomeProdotto, 'quantita' => $quantita);
            }

            $resultArray[$varieta] = $prodottiVarieta;
        } catch (PDOException $e) {
            echo "Error : " . $e->getMessage();
        }
    }

    return $resultArray;
}

/**
 * *Funzione per determinare Il numero di bottiglie Per varietà E id_prod .
 */
function sommaQuantitaPerParola($varietà, $parolaChiave)
{
    $somma = 0;

    if (isset($varietà) && is_array($varietà)) {
        foreach ($varietà as $prodotto) {
            foreach ($prodotto as $dettaglio) {
                // Verifica se la parola chiave è presente nel nome del prodotto
                if (stripos($dettaglio['nome'], $parolaChiave) !== false) {
                    // Aggiungi la quantità del prodotto alla somma
                    $somma += $dettaglio['quantita'];
                }
            }
        }
    }

    return $somma;
}

/**
 * *Funzione Per gli agenti grafici e statistiche.
 */
function analisiImponibileAgenteDiretto($anno)
{
    $tot = analisiImponibileNetto($anno); //imponibile netto totale
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
            CASE
                WHEN a.id IS NOT NULL THEN 'Agente'
                ELSE 'Cliente'
            END AS tipo,
            SUM(f.imp_netto) AS totale_imponibile,
            (SUM(f.imp_netto) / $tot) * 100 AS percentuale
        FROM
            fatture f
        LEFT JOIN
            agenti a ON f.sigla = a.sigla
        WHERE
            YEAR(f.data_f) = :anno
        GROUP BY
            tipo
        ORDER BY
            totale_imponibile DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funzione per determinare L'imponibile derivante da agenti.
function analisiImponibilePerAgente($anno)
{
    $tot = analisiImponibileNetto($anno); // imponibile netto totale

    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
            a.nome_agente AS nome_agente,
            SUM(f.imp_netto) AS imponibile,
            (SUM(f.imp_netto) / $tot) * 100 AS percentuale
        FROM
            fatture f
        JOIN
            agenti a ON f.sigla = a.sigla
        WHERE
            YEAR(f.data_f) = :anno
        GROUP BY
            a.id
        ORDER BY
            imponibile DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera il risultato come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funziona per determinare i dati cliente Partendo dall'id_cfic
function getClienteById($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT * FROM `clienti` WHERE id_cfic=:id_cfic";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare imponibile netto per ciascun cliente In base al suo id_cfic
function analisiImponibilePerCliente($anno, $id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        c.nome AS nome_cliente,
        SUM(f.imp_netto) AS imponibile
    FROM
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
    WHERE
        YEAR(f.data_f) = :anno AND c.id_cfic = :id_cfic
    GROUP BY
        c.id_cfic
    ORDER BY
        imponibile DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare l'imponibile netto Totale per ciascun cliente
function imponibilePerClienteTotale($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        SUM(f.imp_netto) AS imponibile
    FROM
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
    WHERE
       c.id_cfic = :id_cfic";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale = $row['imponibile'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }

    //Quello che è stato pagato paid
    try {
        $query = "SELECT
        SUM(f.imp_netto) AS imponibile
    FROM
        
        fatture f
    JOIN
        clienti c ON f.id_cfic = c.id_cfic
    WHERE
       c.id_cfic = :id_cfic AND f.status='paid'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_pagato = $row['imponibile'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    //Quello che è stato pagato not_paid
    try {
        $query = "SELECT
        SUM(f.imp_netto) AS imponibile
    FROM
            
            fatture f
        JOIN
            clienti c ON f.id_cfic = c.id_cfic
        WHERE
        c.id_cfic = :id_cfic AND f.status='not_paid'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_non_pagato = $row['imponibile'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    //Quello che è stato pagato not_paid e scaduto
    try {
        $query = "SELECT
        SUM(f.imp_netto) AS imponibile
    FROM 
            fatture f
        JOIN
            clienti c ON f.id_cfic = c.id_cfic
        WHERE
        c.id_cfic = :id_cfic AND f.status='not_paid' AND f.data_scadenza < CURDATE()";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_non_pagato_scaduto = $row['imponibile'];
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    $array = array(
        'totale' => arrotondaEFormatta($totale),
        'totale_pagato' => arrotondaEFormatta($totale_pagato),
        'totale_non_pagato' => arrotondaEFormatta($totale_non_pagato),
        'totale_non_pagato_scaduto' => arrotondaEFormatta($totale_non_pagato_scaduto)
    );
    return $array;
}
//Funziona per determinare gli anni in cui ci sono dati per un determinato cliente .
function anniConFatturePerCliente($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        $query = "SELECT DISTINCT YEAR(data_f) AS anno
                  FROM fatture
                  WHERE id_cfic = :id_cfic";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $anni = array();
        foreach ($result as $row) {
            $anni[] = $row['anno'];
        }

        return $anni;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
//Funziona per determinare l'imponibile netto Totale Per un cliente in un determinato anno per ciascuno dei 12 mesi .
function imponibilePerClienteMese($anno, $id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        $query = "SELECT
            MONTH(data_f) AS mese,
            SUM(imp_netto) AS imponibile
        FROM
            fatture
        JOIN
            clienti ON fatture.id_cfic = clienti.id_cfic
        WHERE
            YEAR(data_f) = :anno AND clienti.id_cfic = :id_cfic
        GROUP BY
            mese";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_INT);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Inizializza l'array con valori di default a 0 per tutti i mesi
        $array = array_fill(1, 12, '0');

        // Aggiorna i valori degli imponibili effettivamente presenti
        foreach ($result as $row) {
            $array[$row['mese']] = $row['imponibile'];
        }

        return $array;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//

function dettagliOrdiniPerAnno($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        $anni = anniConFatturePerCliente($id_cfic);
        $dettagli_ordini = array();

        foreach ($anni as $anno) {
            // Numero totale di ordini effettuati
            $query_totale_ordini = "SELECT COUNT(*) AS totale_ordini, SUM(imp_tot) AS totale_imponibile FROM fatture WHERE id_cfic = :id_cfic AND YEAR(data_f) = :anno";
            $stmt_totale_ordini = $db->prepare($query_totale_ordini);
            $stmt_totale_ordini->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
            $stmt_totale_ordini->bindParam(':anno', $anno, PDO::PARAM_INT);
            $stmt_totale_ordini->execute();
            $result_totale_ordini = $stmt_totale_ordini->fetch(PDO::FETCH_ASSOC);
            $totale_ordini = $result_totale_ordini['totale_ordini'];
            $totale_imponibile = $result_totale_ordini['totale_imponibile'];

            // // Numero di ordini scaduti
            // $query_ordini_scaduti = "SELECT COUNT(*) AS ordini_scaduti FROM fatture WHERE id_cfic = :id_cfic AND YEAR(data_f) = :anno AND data_scadenza < CURRENT_DATE";
            // $stmt_ordini_scaduti = $db->prepare($query_ordini_scaduti);
            // $stmt_ordini_scaduti->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
            // $stmt_ordini_scaduti->bindParam(':anno', $anno, PDO::PARAM_INT);
            // $stmt_ordini_scaduti->execute();
            // $result_ordini_scaduti = $stmt_ordini_scaduti->fetch(PDO::FETCH_ASSOC);
            // $ordini_scaduti = $result_ordini_scaduti['ordini_scaduti'];

            // Aggiungi i dettagli per l'anno corrente all'array
            $dettagli_ordini[] = array(
                'anno' => $anno,
                'totale_imponibile' => $totale_imponibile,
                'totale_ordini' => $totale_ordini,
                'media_imponibile' => round($totale_imponibile / $totale_ordini)
                // ,
                // 'ordini_scaduti' => $ordini_scaduti,
            );
        }

        return $dettagli_ordini;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//Funziona per determinare il totale delle bottiglie di una determinata varietà in un determinato anno
function analisiBottigliePerVarietaAnno($anno, $varieta)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        lp.nome_prodotto AS nome_prodotto,
        SUM(p.qta) AS quantita_prodotto
    FROM
        fatture f
        INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
        INNER JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        YEAR(f.data_f) = :anno AND lp.varieta= :varieta
        GROUP BY
        lp.nome_prodotto";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->bindParam(':varieta', $varieta, PDO::PARAM_STR);
        $stmt->execute();
        // Recupera i risultati come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci i risultati
        return $result;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare il totale delle bottiglie di una determinata tipo (bianchi o rossi) in un determinato anno
function analisiBottigliePerTipoAnno($anno, $tipo)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
         lp.varieta,
        SUM(p.qta) AS quantita_prodotto
    FROM
        fatture f
        INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
        JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        YEAR(f.data_f) = :anno AND lp.tipo= :tipo
        GROUP BY
            lp.varieta";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        // Recupera i risultati come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci i risultati
        return $result;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare il totale delle bottiglie di una determinata varietà in un determinato cliente
function analisiBottigliePerVarietaCliente($id_cfic, $varieta)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        SUM(p.qta) AS quantita_prodotto
    FROM
        fatture f
        INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
        JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        f.id_cfic = :id_cfic AND lp.varieta= :varieta";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->bindParam(':varieta', $varieta, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $qta_cliente = $row['quantita_prodotto'];
        if ($qta_cliente === null) {
            $qta_cliente = '0';
        }
        return $qta_cliente;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

//Funziona per determinare il totale delle bottiglie di una determinata tipo (bianchi o rossi) in un determinato cliente
function analisiBottigliePerTipoCliente($id_cfic, $tipo)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
            lp.varieta,
            SUM(p.qta) AS quantita_prodotto
        FROM
            fatture f
            INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
            INNER JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
            INNER JOIN clienti c ON f.id_cfic = c.id_cfic
        WHERE
            c.id_cfic = :id_cfic AND lp.tipo = :tipo
        GROUP BY
            lp.varieta";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->execute();

        // Recupera i risultati come array associativo
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restituisci i risultati
        return $result;
    } catch (PDOException $e) {
        // Gestisci gli errori in modo appropriato, ad esempio, loggandoli o restituendo un messaggio di errore
        echo "Error: " . $e->getMessage();
        return false; // Ritorna un valore significativo in caso di errore
    }
}

//Funziona per determinare il totale delle bottiglie di Un determinato cliente
function analisiBottigliePerCliente($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');
    try {
        $query = "SELECT
        SUM(p.qta) AS quantita_prodotto
    FROM
        fatture f
        INNER JOIN prodotti p ON f.id_ffic = p.id_ffic
        JOIN lista_prodotti lp ON p.id_prod = lp.prod_id
    WHERE
        f.id_cfic = :id_cfic";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $qta_cliente = $row['quantita_prodotto'];
        if ($qta_cliente === null) {
            $qta_cliente = '0';
        }
        return $qta_cliente;
    } catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function Importanza_cliente($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        // Ottieni la data corrente
        $data_corrente = date('Y-m-d');

        // Calcola la data di inizio 12 mesi fa
        $data_inizio_12_mesi_fa = date('Y-m-d', strtotime('-12 months', strtotime($data_corrente)));

        // Query per ottenere il totale imponibile degli ultimi 12 mesi
        $query = "SELECT
                    SUM(imp_netto) AS totale_imponibile
                  FROM
                    fatture
                  WHERE
                data_f BETWEEN :data_inizio_12_mesi_fa AND :data_corrente";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':data_inizio_12_mesi_fa', $data_inizio_12_mesi_fa, PDO::PARAM_STR);
        $stmt->bindParam(':data_corrente', $data_corrente, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_imponibile = $result['totale_imponibile'];

        // Ora puoi calcolare la percentuale rispetto al totale degli ordini (puoi ottenere il totale degli ordini come preferisci)
        // Ad esempio, se hai una funzione totaleOrdini() che restituisce il totale degli ordini per il cliente, puoi fare così:
        $query = "SELECT
        SUM(imp_netto) AS totale_imponibile
      FROM
        fatture
      WHERE
    id_cfic = :id_cfic AND
    data_f BETWEEN :data_inizio_12_mesi_fa AND :data_corrente";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->bindParam(':data_inizio_12_mesi_fa', $data_inizio_12_mesi_fa, PDO::PARAM_STR);
        $stmt->bindParam(':data_corrente', $data_corrente, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_ordini = $result['totale_imponibile'];
        // Calcola la percentuale
        echo round(($totale_ordini / $totale_imponibile) * 100);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false; // Ritorna un valore significativo in caso di errore
    }
}

//Funziona per determinare l'affidabilità del cliente
function Affidabilita_cliente($id_cfic)
{
    include(__DIR__ . '/../../include/configpdo.php');

    try {
        // Ottieni la data corrente
        $data_corrente = date('Y-m-d');

        //Seleziono tutte le fatture del cliente
        $query = "SELECT
                    *
                  FROM
                    fatture
                  WHERE
                id_cfic = :id_cfic";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_cfic', $id_cfic, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $affidabilita = 0;
        $n = 1; //Contatore per il numero di fatture
        foreach ($result as $row) {
            $data_fattura = $row['data_f'];
            $data_scadenza = $row['data_scadenza'];
            $status = $row['status'];
            if ($status == 'not_paid' && $data_scadenza < $data_corrente) {
                //Calcola il numero di giorni tra la data scadenza e la data corrente
                $data1 = new DateTime($data_scadenza);
                $data2 = new DateTime($data_corrente);
                $interval = $data1->diff($data2);
                $giorni = $interval->format('%a');
                if ($giorni > 30) {
                    $giorni = 30;
                }
                //Togli uno per ogni giorno di ritardo
                $affidabilita += 100 - $giorni;
            } else { //Se non è scaduto o è stato pagato

                //Se è stato pagato calcola la differenza tra la data di scadenza e la data di pagamento .
                if ($status == 'paid') {
                    $data_pagamento = $row['data_pagamento'];
                    $data1 = new DateTime($data_scadenza);
                    $data2 = new DateTime($data_pagamento);
                    $interval = $data1->diff($data2);
                    $giorni = $interval->format('%a');
                    if ($giorni > 30) {
                        $giorni = 20;
                    }
                    //Togli uno per ogni giorno di ritardo
                    $affidabilita += 100 - $giorni;
                } else {
                    $affidabilita += 100;
                }
            }
            $n++;
        }
        $affidabilita = $affidabilita / $n;
        echo round($affidabilita);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false; // Ritorna un valore significativo in caso di errore
    }
}
