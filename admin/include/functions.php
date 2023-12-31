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

//funzione per prelevare le fatture da fatture in cloud
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


        // else {
        //     //altrimenti prelevo le fatture in base al tipo
        //     $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        //     $issuedEInvoices = $issuedEInvoicesApi->getIssuedDocument($firstCompanyId, $id_doc);

        // }

        // $firstCompanyId = $companies->getData()->getCompanies()[1]->getId();
        // $campi = "amount_net,entity,amount_vat";

        // $q = "date > '2023-10-21'";
        // // id, tipo, campi  , detailed, , page, per_page, filtro
        // $issuedEInvoices = $issuedEInvoicesApi->listIssuedDocuments($firstCompanyId, 'invoice', null, 'detailed', null, $page, 50, $q);
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

//funzione per prelevare la fattura singola da fatture in cloud
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

//Funzione per prelevare lo status della fattura
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
