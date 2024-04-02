<?
    session_start();
	include_once('../../config.php');
	include_once($root.'/inc/funzioni.php');
    if (! isset($_SESSION["ente"])) {
        header("HTTP/1.0 403 Forbidden");
    } else {
        $columns = array('colore','b_gare.codice', 'b_gare.id_suaff', 'b_gare.stato', 'cig', 'b_gare.tipologia', 'b_criteri.criterio', 'b_procedure.nome', 'b_gare.oggetto', 'b_enti.denominazione', 'b_gare.data_scadenza');

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
            $searchable = array('b_gare.id', "b_gare.id_suaff", "b_lotti.cig", 'b_gare.cig', 'b_stati_gare.titolo', 'b_tipologie.tipologia', 'b_criteri.criterio', 'b_procedure.nome', 'b_gare.oggetto', 'b_enti.denominazione');
            foreach ($searchable as $key) {
                $sql_where .= "{$key} LIKE :value OR ";
            }
            $sql_where = substr($sql_where, 0, -3);
            $sql_where = "AND ({$sql_where})";
        }

        $sql_from = "";
        $sql_count = "SELECT COUNT( DISTINCT b_gare.codice )";
        $sql_select = "SELECT b_gare.*, 
                              b_tipologie.tipologia AS tipologia,
                              b_ente_gestore.dominio, 
                              b_enti.denominazione,
                              b_enti.provincia,
                              b_criteri.criterio AS criterio, 
                              b_procedure.nome AS procedura,
                              b_stati_gare.titolo AS fase, 
                              b_stati_gare.colore  ";

        if (!isset($_SESSION["codice_utente"])) {
            $sql_from .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice 
			              JOIN b_criteri ON b_gare.criterio = b_criteri.codice 
			              JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice 
			              JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase 
			              JOIN b_enti ON b_gare.codice_ente = b_enti.codice 
                          JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice 
                          LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara
			              WHERE pubblica = '2' AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
			if (isset($_POST["scadute"])) {
				if ($_POST["scadute"]) {
					$sql_from .= " AND b_gare.data_scadenza < NOW() ";
				} else {
					$sql_from .= " AND b_gare.data_scadenza >= NOW() ";
				}
			}
			if (! empty($_POST["codice_ente"])) {
				$bind[":codice_ente_filtro"] = $_POST["codice_ente"];
				$sql_from .= " AND codice_ente = :codice_ente_filtro ";
			}
			if (! empty($_POST["esiti"])) {
				$sql_from .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
			}
        } else {
            $sql_select = "SELECT b_gare.*, b_enti.denominazione, b_enti.provincia, b_tipologie.tipologia AS tipologia, b_ente_gestore.dominio, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore ";
            if (is_operatore()) {
                $bind[":codice_utente"] = $_SESSION["codice_utente"];
				$sql_from .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice 
                              JOIN b_criteri ON b_gare.criterio = b_criteri.codice 
                              JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice 
                              JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase 
                              JOIN b_enti ON b_gare.codice_ente = b_enti.codice 
                              JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice 
                              LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara
                              LEFT JOIN r_inviti_gare ON b_gare.codice = r_inviti_gare.codice_gara 
                              WHERE (codice_gestore = :codice_ente OR codice_ente = :codice_ente) 
                              AND (pubblica = '2' OR (pubblica = '1' AND ((b_procedure.invito = 'N' AND r_inviti_gare.codice_utente IS NULL) OR (b_procedure.invito = 'S' AND r_inviti_gare.codice_utente = :codice_utente)))) ";
                if (isset($_POST["scadute"])) {
                    if ($_POST["scadute"]) {
                        $sql_from .= " AND b_gare.data_scadenza < NOW() ";
                    } else {
                        $sql_from .= " AND b_gare.data_scadenza >= NOW() ";
                    }
                }
				if (!empty($_POST["codice_ente"])) {
					$bind[":codice_ente_filtro"]=$_POST["codice_ente"];
					$sql_from .= " AND codice_ente = :codice_ente_filtro ";
				}
				if (!empty($_POST["esiti"])) {
					$sql_from .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
				}
            } else {
				$sql_from .= "FROM b_gare JOIN b_procedure ON b_gare.procedura = b_procedure.codice 
				              JOIN b_criteri ON b_gare.criterio = b_criteri.codice 
				              JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice 
				              JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase 
				              JOIN b_enti ON b_gare.codice_ente = b_enti.codice 
                              JOIN b_enti AS b_ente_gestore ON b_gare.codice_gestore = b_ente_gestore.codice 
                              LEFT JOIN b_lotti ON b_gare.codice = b_lotti.codice_gara
				              WHERE (pubblica > 0) AND (codice_gestore = :codice_ente OR codice_ente = :codice_ente) ";
                if (isset($_POST["scadute"])) {
                    if ($_POST["scadute"]) {
                        $sql_from .= " AND b_gare.data_scadenza < NOW() ";
                    } else {
                        $sql_from .= " AND b_gare.data_scadenza >= NOW() ";
                    }
                }
				if (!empty($_POST["codice_ente"])) {
					$bind[":codice_ente_filtro"]=$_POST["codice_ente"];
					$sql_from .= " AND codice_ente = :codice_ente_filtro ";
				}
				if (!empty($_POST["esiti"])) {
					$sql_from .= " AND (b_gare.stato = 4 OR b_gare.stato >= 7) ";
				}
            }
        }

        $total = $pdo->go("{$sql_count} {$sql_from} {$sql_where} ", $bind)->fetch(PDO::FETCH_COLUMN, 0);
        $gare = $pdo->go("{$sql_select} {$sql_from} {$sql_where} GROUP BY b_gare.codice {$sql_order} {$sql_limit}",  $bind)->fetchAll(PDO::FETCH_ASSOC);
        $result = array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => array()
        );


        $lotti = $pdo->prepare("SELECT cig FROM b_lotti WHERE codice_gara = :codice_gara AND annullata = 'N'");

        foreach ($gare as $gara) {
            if ($gara["stato"] == 3 && strtotime($gara["data_scadenza"]) < time()) {
                $gara["colore"] = $config["colore_scaduta"];
                $gara["fase"] = "Scaduta";
            }

            $lotti->bindValue(":codice_gara", $gara["codice"]);
            $lotti->execute();
            if ($lotti->rowCount() > 0) {
                $gara["cig"] = array();
                while($lotto = $lotti->fetch(PDO::FETCH_ASSOC)) if (!empty($lotto["cig"])) $gara["cig"][] = $lotto["cig"];
                $gara["cig"] = implode("<br>",$gara["cig"]);
            }
            $row = array();
            $customPath = "personal/table_{$_SESSION["ente"]["codice"]}.php";
            if (file_exists($customPath)) {
                include($customPath);
            } else {
                $row["id"] = $gara["id"];
                $row["id_suaff"] = $gara["id_suaff"];
                $row["colore"] = '<div style="height: 100%; width:100%; position:absolute; top:0; left:0; right:0; bottom:0; background-color: #'.$gara["colore"].'"></div>';
                $row["cig"] = $gara["cig"];
                $row["stato"] = $gara["fase"];
                $row["tipo"] = $gara["tipologia"];
                $row["criterio"] = $gara["criterio"];
                $row["procedura"] = $gara["procedura"];
                ob_start();
                if ($gara["annullata"] == "S") { ?><strong><?= traduci('Annullata') ?> - <?= $gara["numero_annullamento"] ?> - <?= mysql2date($gara["data_annullamento"]) ?></strong> -<? } ?>
                <a href="<?= $config["protocollo"] ?><?= $gara["dominio"] ?>/gare/id<? echo $gara["codice"] ?>-dettaglio" title="<?= traduci('dettagli') ?>"><? echo $gara["oggetto"] ?></a><?
                $row["oggetto"] = ob_get_clean();
                if ($_SESSION["ente"]["tipo"] == "SUA") $row["denominazione_ente"] = $gara["denominazione"];
                $row["scadenza"] = mysql2datetime($gara["data_scadenza"]);
            }
            $result["data"][] = $row;
        }

    }

    echo json_encode($result);
    die();




    if (! isset($_SESSION["codice_utente"]) || ! isset($_SESSION["ente"])) {
        header("HTTP/1.0 403 Forbidden");
        die();
    } else {
        if(! check_permessi("gare",$_SESSION["codice_utente"])) {
            header("HTTP/1.0 403 Forbidden");
        } else {

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
                $searchable = array('b_gare.id', 'cig', 'b_stati_gare.titolo', 'b_tipologie.tipologia', 'b_criteri.criterio', 'b_procedure.nome', 'oggetto', 'b_enti.denominazione');
                foreach ($searchable as $key) {
                    $sql_where .= "{$key} LIKE :value OR ";
                }
                $sql_where = substr($sql_where, 0, -3);
                $sql_where = "AND ({$sql_where})";
            }

            $sql_count = "SELECT COUNT(b_gare.codice)";
            $sql_select = "SELECT b_gare.*, b_enti.denominazione as denominazione_ente, b_tipologie.tipologia AS tipologia, b_criteri.criterio AS criterio, b_procedure.nome AS procedura, b_stati_gare.titolo AS fase, b_stati_gare.colore ";

            $sql_from = "FROM b_gare JOIN b_stati_gare ON b_gare.stato = b_stati_gare.fase JOIN b_procedure ON b_gare.procedura = b_procedure.codice JOIN b_criteri ON b_gare.criterio = b_criteri.codice JOIN b_tipologie ON b_gare.tipologia = b_tipologie.codice JOIN b_enti ON b_gare.codice_ente = b_enti.codice ";
            if ($_SESSION["gerarchia"] > 1) $sql_from .= "JOIN b_permessi ON b_permessi.codice_gara = b_gare.codice ";
            $sql_from .= "WHERE codice_gestore = :codice_ente ";
            if ($_SESSION["gerarchia"] > 0 && $_SESSION["ente"]["codice"] != $_SESSION["record_utente"]["codice_ente"]) {
                $bind[":codice_ente_utente"] = $_SESSION["record_utente"]["codice_ente"];
                $strsql .= " AND b_gare.codice_ente = :codice_ente_utente";
            }
            if ($_SESSION["gerarchia"] > 1) {
                $bind[":codice_utente"] = $_SESSION["codice_utente"];
                $sql_from .= " AND b_permessi.codice_utente = :codice_utente ";
            }

            $total = $pdo->go("{$sql_count} {$sql_from} {$sql_where}", $bind)->fetch(PDO::FETCH_COLUMN, 0);
            $gare = $pdo->go("{$sql_select} {$sql_from} {$sql_where} {$sql_order} {$sql_limit}",  $bind)->fetchAll(PDO::FETCH_ASSOC);
            $result = array(
                "draw" => intval($_POST['draw']),
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "data" => array(),
                "query" => "{$sql_select} {$sql_from} {$sql_where} {$sql_order} {$sql_limit}"
            );

            $sth_quesiti = $pdo->prepare("SELECT COUNT(b_quesiti.codice) FROM b_quesiti LEFT JOIN b_risposte ON b_quesiti.codice = b_risposte.codice_quesito WHERE b_quesiti.codice_gara = :codice_gara AND (b_risposte.testo = '' OR b_risposte.testo IS NULL) AND b_quesiti.attivo = 'N'");
            $sth_sopralluoghi = $pdo->prepare("SELECT COUNT(b_sopralluoghi.codice) FROM b_sopralluoghi WHERE codice_gara = :codice_gara AND appuntamento IS NULL");

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
                $row["colore"] = '<div style="height: 100%; width:100%; position:absolute; top:0; left:0; right:0; bottom:0; background-color: #'.$gara["colore"].'"></div>';
                $row["cig"] = $gara["cig"];
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
                $row["oggetto"] = ob_get_clean();
                $result["data"][] = $row;
            }

            echo json_encode($result);
            die();
        }
    }
?>