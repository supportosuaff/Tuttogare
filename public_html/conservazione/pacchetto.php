<?
    session_start();
    include("../../config.php");
    include_once($root."/inc/funzioni.php");

    if(! empty($_SESSION["codice_utente"]) && ! empty($_GET["codice"]) && ! empty($_GET["operazione"])) {

        session_write_close();

        $operazioni = ["download", "send"];

        $sql = "SELECT * FROM b_conservazione WHERE codice = :codice";
        $bind = [':codice' => $_GET['codice']];

        if($_SESSION["gerarchia"] > 0) {
            $sql = "{$sql} AND (b_conservazione.codice_gestore = :codice_ente OR b_conservazione.codice_ente = :codice_ente)";
            $bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"];
        }

        $pacchetto = $pdo->bindAndExec($sql, $bind);
        if($pacchetto->rowCount() > 0) {

            $pacchetto = $pacchetto->fetch(PDO::FETCH_ASSOC);

            $files = $pdo->bindAndExec("SELECT * FROM r_conservazione_file WHERE codice_pacchetto = :codice_pacchetto", [":codice_pacchetto" => $pacchetto["codice"]]);
            if($files->rowCount() > 0) {

                $check = check_permessi("conservazione", $_SESSION["codice_utente"]);

                if (!$check) {

                    $bind = array(":codice_gara"=>$pacchetto["codice_oggetto"]);
                    switch ($pacchetto["sezione"]) {
                        case 'gara':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_gare ";
                            if ($_SESSION["gerarchia"] > 1) $sql_check .= "JOIN b_permessi ON b_permessi.codice_gara = b_gare.codice ";
                            $sql_check .= "WHERE b_gare.codice = :codice_gara AND (b_gare.codice_ente = :codice_ente OR b_gare.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_gare.codice_ente = :codice_utente_ente OR b_gare.codice_gestore = :codice_utente_ente) ";
                                if ($_SESSION["gerarchia"] > 1) {
                                    $bind[":codice_utente"] = $_SESSION["codice_utente"];
                                    $sql_check.= " AND b_permessi.codice_utente = :codice_utente";
                                }
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                            break;
                        case 'concorsi':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_concorsi ";
                            if ($_SESSION["gerarchia"] > 1) $sql_check .= "JOIN b_permessi_concorsi ON b_permessi_concorsi.codice_gara = b_concorsi.codice ";
                            $sql_check .= "WHERE b_concorsi.codice = :codice_gara AND (b_concorsi.codice_ente = :codice_ente OR b_concorsi.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_concorsi.codice_ente = :codice_utente_ente OR b_concorsi.codice_gestore = :codice_utente_ente) ";
                                if ($_SESSION["gerarchia"] > 1) {
                                    $bind[":codice_utente"] = $_SESSION["codice_utente"];
                                    $sql_check.= " AND b_permessi_concorsi.codice_utente = :codice_utente";
                                }
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                            break;
                        case 'mercato':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_bandi_mercato WHERE b_bandi_mercato.codice = :codice_gara ";
                            $sql_check .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_bandi_mercato.codice_ente = :codice_utente_ente OR b_bandi_mercato.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                            break;
                        case 'sda':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

                            $sql_check = "SELECT * FROM b_bandi_sda WHERE b_bandi_sda.codice = :codice_gara ";
                            $sql_check .= "AND (b_bandi_sda.codice_ente = :codice_ente OR b_bandi_sda.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_bandi_sda.codice_ente = :codice_utente_ente OR b_bandi_sda.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                            break;
                        case 'albo':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

                            $sql_check = "SELECT * FROM b_bandi_albo WHERE b_bandi_albo.codice = :codice_gara ";
                            $sql_check .= "AND (b_bandi_albo.codice_ente = :codice_ente OR b_bandi_albo.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_bandi_albo.codice_ente = :codice_utente_ente OR b_bandi_albo.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                            break;
                        case 'dialogo':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_bandi_dialogo WHERE b_bandi_dialogo.codice = :codice_gara ";
                            $sql_check .= "AND (b_bandi_dialogo.codice_ente = :codice_ente OR b_bandi_dialogo.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_bandi_dialogo.codice_ente = :codice_utente_ente OR b_bandi_dialogo.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                        case 'esecuzione':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_contratti WHERE b_contratti.codice = :codice_gara ";
                            $sql_check .= "AND (b_contratti.codice_ente = :codice_ente OR b_contratti.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_contratti.codice_ente = :codice_utente_ente OR b_contratti.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                        case 'fabbisogno':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_fabbisogno WHERE b_fabbisogno.codice = :codice_gara ";
                            $sql_check .= "AND b_fabbisogno.codice_gestore = :codice_ente ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND b_fabbisogno.codice_gestore = :codice_utente_ente ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) {
                                $check = true;
                            } else {
                                unset($bind[":codice_ente"]);
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check = "SELECT * FROM r_enti_fabbisogno WHERE codice_ente = :codice_utente_ente AND codice_fabbisogno = :codice_gara";
                                $ris_check = $pdo->bindAndExec($sql_check,$bind);
                                if ($ris_check->rowCount() > 0) $check = true;
                            }
                        case 'progetti':
                            $bind[":codice_ente"] = $_SESSION["ente"]["codice"];
                            $sql_check = "SELECT * FROM b_progetti_investimento WHERE b_progetti_investimento.codice = :codice_gara ";
                            $sql_check .= "AND (b_progetti_investimento.codice_ente = :codice_ente OR b_progetti_investimento.codice_gestore = :codice_ente) ";
                            if ($_SESSION["gerarchia"] > 0) {
                                $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
                                $sql_check .= " AND (b_progetti_investimento.codice_ente = :codice_utente_ente OR b_progetti_investimento.codice_gestore = :codice_utente_ente) ";
                            }
                            $ris_check = $pdo->bindAndExec($sql_check,$bind);
                            if ($ris_check->rowCount() > 0) $check = true;
                        default:
                            $check = false;
                            break;
                    }

                }

                if ($check) {

                    if($_GET["operazione"] == "download") {

                        if(empty($pacchetto["download"]) && $pacchetto["download"] != "L") {

                            $pdo->bindAndExec("UPDATE b_conservazione SET download = 'L' WHERE codice = :codice", [":codice" => $pacchetto["codice"]]);
                            $pdo->bindAndExec("INSERT INTO w_conservazione (codice_conservazione, operazione, stato) VALUES (:codice, 'DOWNLOAD', 'A')", [":codice" => $pacchetto["codice"]]);

                        } elseif ($pacchetto["download"] == "C") {

                            $percorso = "{$config["arch_folder"]}/conservazione/{$pacchetto["codice_ente"]}/{$pacchetto["codice"]}";
                            $archivio = "{$percorso}/ARCHIVIO-ID{$pacchetto["codice_pacchetto"]}-" . date('Y') . ".zip";

                            if(file_exists($archivio)) {

                                header('Content-Description: File Transfer');
                                header("Content-Type: application/zip");
                                header('Content-Disposition: attachment; filename="'.basename($archivio).'"');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate');
                                header('Pragma: public');
                                header("Content-Transfer-Encoding: Binary");
                                header('Content-Length: ' . filesize($archivio));

                                $chunk = 10 * 1024 * 1024;
                                $handle = fopen($archivio, 'rb');
                                while (!feof($handle)) {

                                    $buffer = fread($handle, $chunk);
                                    echo $buffer;
                                    ob_flush();
                                    flush();
                                }

                                fclose($handle);
                                die();

                            } else {

                                $pdo->bindAndExec("UPDATE b_conservazione SET download = 'L' WHERE codice = :codice", [":codice" => $pacchetto["codice"]]);
                                $pdo->bindAndExec("INSERT INTO w_conservazione (codice_conservazione, operazione, stato) VALUES (:codice, 'DOWNLOAD', 'A')", [":codice" => $pacchetto["codice"]]);

                            }

                        }

                    } else if($_GET["operazione"] == "send") {

                        if(empty($pacchetto["send"]) && $pacchetto["send"] != "L") {

                            $pdo->bindAndExec("UPDATE b_conservazione SET send = 'L' WHERE codice = :codice", [":codice" => $pacchetto["codice"]]);
                            $pdo->bindAndExec("INSERT INTO w_conservazione (codice_conservazione, operazione, stato) VALUES (:codice, 'SEND', 'A')", [":codice" => $pacchetto["codice"]]);

                        }

                    }

                }


            }

        }

    }


?>

<meta http-equiv="refresh" content="0;URL=/conservazione">