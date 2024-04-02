<?
    session_start();
	include_once('../../config.php');
	include_once($root.'/inc/funzioni.php');
    if (! isset($_SESSION["codice_utente"]) || ! isset($_SESSION["ente"])) {
        header("HTTP/1.0 403 Forbidden");
        die();
    } else {
        if(! check_permessi("gare",$_SESSION["codice_utente"])) {
            header("HTTP/1.0 403 Forbidden");
        } else {
            $permessi_contratti = check_permessi("contratti",$_SESSION["codice_utente"]);

            $columns = array('colore', 'b_gare.codice', 'b_gare.id_suaff', 'b_gare.cig', 'b_stati_gare.titolo', 'b_tipologie.tipologia', 'b_criteri.criterio', 'b_procedure.nome', 'b_gare.oggetto', 'denominazione_ente');

            /** INDEX */
            $index = "id";

            /** PAGING */
            $sql_limit = "";
            if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' && is_numeric( $_POST['start'] ) && is_numeric( $_POST['length'] )) {
                $sql_limit = "LIMIT {$_POST['start']}, {$_POST['length']}";
            }

            /** ORDER */
            $sql_order = "";
            if(! empty($_POST["order"])) {
                $sql_order = "ORDER BY  ";
                foreach ($_POST["order"] as $info) {
                    if(! empty($columns[$info["column"]])) $sql_order .= "{$columns[$info["column"]]} {$info["dir"]}, ";
                }
        		$sql_order = substr_replace($sql_order, "", -2);
                if($sql_order == "ORDER BY") $sql_order = "";
            }

            $bind = array();
            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

            $sql_where = "";
            if(! empty($_POST["search"]["value"])) {
                $bind[":value"] = "%{$_POST["search"]["value"]}%";
                $searchable = array('b_gare.id', 'b_gare.id_suaff', 'b_gare.cig', 'b_lotti.cig', 'b_stati_gare.titolo', 'b_tipologie.tipologia', 'b_criteri.criterio', 'b_procedure.nome', 'b_gare.oggetto', 'b_enti.denominazione');
                foreach ($searchable as $key) {
                    $sql_where .= "{$key} LIKE :value OR ";
                }
                $sql_where = substr($sql_where, 0, -3);
                $sql_where = "AND ({$sql_where})";
            }

            $sql_count = "SELECT COUNT( DISTINCT b_gare.codice)";
            $sql_select = "SELECT b_gare.*, GROUP_CONCAT(b_lotti.cig) AS cig_lotti, b_enti.denominazione as denominazione_ente, b_tipologie.tipologia AS tipologia, b_gare.tipologia AS codice_tipologia, b_gare.procedura AS codice_procedura, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore ";

            $sql_from = "FROM b_gare
                         JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase
                         JOIN b_procedure ON b_gare.procedura = b_procedure.codice
                         JOIN b_criteri ON b_gare.criterio = b_criteri.codice
                         JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice
                         JOIN b_enti ON b_gare.codice_ente = b_enti.codice
                         LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara ";
            if ($_SESSION["gerarchia"] > 1) $sql_from .= "JOIN b_permessi ON b_permessi.codice_gara = b_gare.codice ";
            $sql_from .= "WHERE codice_gestore = :codice_ente ";
            if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
                $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
                $sql_from .= " AND b_gare.codice_ente = :codice_ente_utente";
            }
            if ($_SESSION["gerarchia"] > 1) {
                $bind[":codice_utente"] = $_SESSION["codice_utente"];
                $sql_from .= " AND b_permessi.codice_utente = :codice_utente ";
            }

            $total = $pdo->go("{$sql_count} {$sql_from} {$sql_where} ", $bind)->fetch(PDO::FETCH_COLUMN, 0);
            $gare = $pdo->go("{$sql_select} {$sql_from} {$sql_where} GROUP BY b_gare.codice {$sql_order} {$sql_limit}",  $bind)->fetchAll(PDO::FETCH_ASSOC);
            $result = array(
                "draw" => intval($_POST['draw']),
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => array(),
                "query" => "{$sql_select} {$sql_from} {$sql_where} {$sql_order} {$sql_limit}"
            );

            $sth_quesiti = $pdo->prepare("SELECT COUNT(b_quesiti.codice) FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito WHERE b_quesiti.codice_gara = :codice_gara AND (b_risposte.testo = '' OR b_risposte.testo IS NULL) AND b_quesiti.attivo = 'N'");
            $sth_sopralluoghi = $pdo->prepare("SELECT COUNT(b_sopralluoghi.codice) FROM b_sopralluoghi WHERE codice_gara = :codice_gara AND appuntamento IS NULL");

            $check_contratto = $pdo->prepare("SELECT codice FROM b_contratti WHERE codice_gara = :codice AND codice_gestore = :codice_gestore");
            $check_contratto->bindValue(":codice_gestore",$_SESSION["ente"]["codice"]);
            $check_firmato = $pdo->prepare("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_firmati'");
            $check_da_firmare = $pdo->prepare("SELECT * FROM `b_allegati` WHERE `sezione` = 'contratti' AND `codice_gara` = :codice_contratto AND `cartella` = 'contratti_da_firmare'");
            $check_firmato_oe = $pdo->prepare("SELECT * FROM `b_allegati_contratto` WHERE `codice_contratto` = :codice_contratto AND `titolo` LIKE 'CONTRATTO FIRMATO DALL%OPERATORE ECONOMICO'");

            $tipologie_lavori = [1,5,7,8,9];

            foreach ($gare as $gara) {
                $gara["sopralluoghi"] = $gara["quesiti"] = 0;

                $sth_quesiti->bindValue(':codice_gara', $gara["codice"]);
                $sth_quesiti->execute();
                $gara["quesiti"] = $sth_quesiti->fetch(PDO::FETCH_COLUMN, 0);

                $sth_sopralluoghi->bindValue(':codice_gara', $gara["codice"]);
                $sth_sopralluoghi->execute();
                $gara["sopralluoghi"] = $sth_sopralluoghi->fetch(PDO::FETCH_COLUMN, 0);

                if (($gara["stato"]==3) && (strtotime($gara["data_scadenza"])<time())) {
                    $gara["colore"] = $config["colore_scaduta"];
                    $gara["fase"] = "Scaduta";
                }

                $row = array();
                $row["id"] = $gara["id"];
                $row["id_suaff"] = $gara["id_suaff"];
                $row["colore"] = '<div style="height: 100%; width:100%; position:absolute; top:0; left:0; right:0; bottom:0; background-color: #'.$gara["colore"].'"></div>';
                $row["cig"] = (!empty($gara["cig_lotti"])) ? str_replace(",","<br>",$gara["cig_lotti"]) : $gara["cig"];
                $row["stato"] = $gara["fase"];
                $row["tipo"] = $gara["tipologia"];
                $row["criterio"] = $gara["criterio"];
                $row["procedura"] = $gara["procedura"];
                if($_SESSION["ente"]["tipo"] == "SUA") $row["denominazione_ente"] = $gara["denominazione_ente"];
                ob_start();
                ?><a href="/gare/pannello.php?codice=<? echo $gara["codice"] ?>" title="Pannello gara"><div style="text-align:right"><?
                if ($gara["quesiti"] > 0 && $gara["stato"] < 4) { ?><span class="relative-badge"><?= $gara["quesiti"] ?> Chiarimenti pendenti</span> <? }
                if ($gara["sopralluoghi"] > 0 && $gara["stato"] < 4) { ?><span class="relative-badge"><?= $gara["sopralluoghi"] ?> Richieste sopralluogo pendenti</span><? }
                ?></div><? echo $gara["oggetto"]; ?></a><?


                if ((($gara["stato"]== 7) || ($gara["stato"]==8)) && $permessi_contratti) {
                    $color_contratto = "#C00";
                    $label = "Contratto non presente";
                    $check_contratto->bindValue(":codice",$gara["codice"]);
                    $check_contratto->execute();
                    if ($check_contratto->rowCount() > 0) {
                        $color_contratto = "#F90";
                        $label = "Contratto in elaborazione";
                        $contratti = $check_contratto->fetchAll(PDO::FETCH_ASSOC);
                        $totale = count($contratti);
                        $firmati = 0;
                        foreach($contratti AS $contratto) {
                            $check_firmato->bindValue(":codice_contratto",$contratto["codice"]);
                            $check_firmato->execute();
                            if($check_firmato->rowCount() > 0) $firmati++;
                        }
                        if ($totale == $firmati) {
                            $color_contratto = "#090";
                            $label = "Contratto firmato";
                        } else {
                            foreach($contratti AS $contratto) {
                                $check_da_firmare->bindValue(":codice_contratto",$contratto["codice"]);
                                $check_da_firmare->execute();
                                if($check_da_firmare->rowCount() > 0) {
                                    $color_contratto = "#FF0";
                                    $label = "Contratto inviato all'OE";
                                    $check_firmato_oe->bindValue(":codice_contratto",$contratto["codice"]);
                                    $check_firmato_oe->execute();
                                    if ($check_firmato_oe->rowCount() > 0) {
                                        $color_contratto = "#DF0";
                                        $label = "Contratto firmato dall'OE";
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    <div class="clear" style="text-align:right">
                        <span class="relative-badge" style="border-left: 10px solid <?= $color_contratto ?>; margin-bottom:10px">
                            <?= $label ?>
                        </span>
                    </div>
                    <?
                }
                $row["oggetto"] = ob_get_clean();
                ob_start();
                $date_atto = mysql2date($gara["data_atto_indizione"]);
                if (!empty($date_atto) && strtotime($gara["data_atto_indizione"]) >= strtotime('2020-07-16') && $gara["stato"] < "98") { // conteggio termini decreto semplificazione 16/07/2020
                    ?>
                    <div style="text-align:center">
                    <?
                    $mesi_conclusione = 4;
                    if ($gara["codice_procedura"] == "11") $mesi_conclusione = 2;
                    if (in_array($gara["codice_tipologia"],$tipologie_lavori) !== false && $gara["prezzoBase"] >= 5548000) {
                        $mesi_conclusione = 6;
                    } else if (in_array($gara["codice_tipologia"],$tipologie_lavori) === false && $gara["prezzoBase"] >= 221000) {
                        $mesi_conclusione = 6;
                    }
                    $avvio = strtotime($gara["data_atto_indizione"]);
                    $deadline = strtotime($gara["data_atto_indizione"] . "+{$mesi_conclusione} month");
                    echo date("d/m/Y",$deadline);
                    $concluso = false; 
                    $date_esito = mysql2date($gara["data_atto_esito"]); 
                    if (!empty($date_esito)) {
                        $concluso = true;
                        $conclusione = strtotime($gara["data_atto_esito"]);
                        
                    } else {
                        $conclusione = time();
                    }
                    $diff = $conclusione - $deadline;
                    $diff = ceil($diff / 60 / 60 / 24);
                    $style="color:#0C0";
                    if ($diff > -10) $style="color:#FC0";
                    if ($diff > 0) $style="color:#C00";
                    ?>
                    <br><span style="<?= $style ?>"><?= $diff ?> giorni</span>
                    </div>
                    <?
                }
                $row["termini"] = ob_get_clean();
                $result["data"][] = $row;
            }

            echo json_encode($result);
            die();
        }
    }
?>