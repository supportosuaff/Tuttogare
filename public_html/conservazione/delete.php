<?
  session_start();
  include("../../config.php");
  include_once($root."/inc/funzioni.php");
;
  if(empty($_SESSION["codice_utente"]) || empty($_POST["codice"])) {
    ?>alert('Non si dispone dei permessi necessari.')<?
    die();
  } else {

    $sql = "SELECT * FROM b_conservazione WHERE codice = :codice";
    $bind = array(':codice' => $_POST['codice']);
    if($_SESSION["gerarchia"] > 0) {$sql .= " AND (b_conservazione.codice_gestore = :codice_ente OR b_conservazione.codice_ente = :codice_ente)"; $bind[":codice_ente"] = $_SESSION["record_utente"]["codice_ente"];}
    $ris = $pdo->bindAndExec($sql, $bind);

    if($ris->rowCount() > 0) {
      $pacchetto = $ris->fetch(PDO::FETCH_ASSOC);
      $edit = check_permessi("conservazione",$_SESSION["codice_utente"]);
      if (!$edit && $pacchetto["stato"] == 0) {
        $bind = array(":codice_gara"=>$pacchetto["codice_oggetto"]);
        if ($pacchetto["sezione"] == "gara") {

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
					if ($ris_check->rowCount() > 0) $edit = true;

				}

				if ($pacchetto["sezione"] == "mercato") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_bandi_mercato WHERE b_bandi_mercato.codice = :codice_gara ";
					$sql_check .= "AND (b_bandi_mercato.codice_ente = :codice_ente OR b_bandi_mercato.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_bandi_mercato.codice_ente = :codice_utente_ente OR b_bandi_mercato.codice_gestore = :codice_utente_ente) ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) $edit = true;

				}

				if ($pacchetto["sezione"] == "sda") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_bandi_sda WHERE b_bandi_sda.codice = :codice_gara ";
					$sql_check .= "AND (b_bandi_sda.codice_ente = :codice_ente OR b_bandi_sda.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_bandi_sda.codice_ente = :codice_utente_ente OR b_bandi_sda.codice_gestore = :codice_utente_ente) ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) $edit = true;

				}

				if ($pacchetto["sezione"] == "albo") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_bandi_albo WHERE b_bandi_albo.codice = :codice_gara ";
					$sql_check .= "AND (b_bandi_albo.codice_ente = :codice_ente OR b_bandi_albo.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_bandi_albo.codice_ente = :codice_utente_ente OR b_bandi_albo.codice_gestore = :codice_utente_ente) ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) $edit = true;

				}

        if ($pacchetto["sezione"] == "dialogo") {

          $bind[":codice_ente"] = $_SESSION["ente"]["codice"];

          $sql_check = "SELECT * FROM b_bandi_dialogo WHERE b_bandi_dialogo.codice = :codice_gara ";
          $sql_check .= "AND (b_bandi_dialogo.codice_ente = :codice_ente OR b_bandi_dialogo.codice_gestore = :codice_ente) ";
          if ($_SESSION["gerarchia"] > 0) {
            $bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
            $sql_check .= " AND (b_bandi_dialogo.codice_ente = :codice_utente_ente OR b_bandi_dialogo.codice_gestore = :codice_utente_ente) ";
          }
          $ris_check = $pdo->bindAndExec($sql_check,$bind);
          if ($ris_check->rowCount() > 0) $edit = true;

        }

				if ($pacchetto["sezione"] == "esecuzione") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_contratti WHERE b_contratti.codice = :codice_gara ";
					$sql_check .= "AND (b_contratti.codice_ente = :codice_ente OR b_contratti.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_contratti.codice_ente = :codice_utente_ente OR b_contratti.codice_gestore = :codice_utente_ente) ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) $edit = true;

				}

				if ($pacchetto["sezione"] == "fabbisogno") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_fabbisogno WHERE b_fabbisogno.codice = :codice_gara ";
					$sql_check .= "AND b_fabbisogno.codice_gestore = :codice_ente ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND b_fabbisogno.codice_gestore = :codice_utente_ente ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) {
						$edit = true;
					} else {
						unset($bind[":codice_ente"]);
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check = "SELECT * FROM r_enti_fabbisogno WHERE codice_ente = :codice_utente_ente AND codice_fabbisogno = :codice_gara";
						$ris_check = $pdo->bindAndExec($sql_check,$bind);
						if ($ris_check->rowCount() > 0) $edit = true;
					}
				}

				if ($pacchetto["sezione"] == "progetti") {

					$bind[":codice_ente"] = $_SESSION["ente"]["codice"];

					$sql_check = "SELECT * FROM b_progetti_investimento WHERE b_progetti_investimento.codice = :codice_gara ";
					$sql_check .= "AND (b_progetti_investimento.codice_ente = :codice_ente OR b_progetti_investimento.codice_gestore = :codice_ente) ";
					if ($_SESSION["gerarchia"] > 0) {
						$bind[":codice_utente_ente"] = $_SESSION["record_utente"]["codice_ente"];
						$sql_check .= " AND (b_progetti_investimento.codice_ente = :codice_utente_ente OR b_progetti_investimento.codice_gestore = :codice_utente_ente) ";
					}
					$ris_check = $pdo->bindAndExec($sql_check,$bind);
					if ($ris_check->rowCount() > 0) $edit = true;

				}
      }
      if ($edit) {
        $sql = "DELETE FROM r_conservazione_file WHERE codice_pacchetto = :codice_pacchetto";
        $ris_files = $pdo->bindAndExec($sql,array(":codice_pacchetto"=>$pacchetto["codice"]));
        $sql = "DELETE FROM b_conservazione WHERE codice = :codice_pacchetto";
        $ris_files = $pdo->bindAndExec($sql,array(":codice_pacchetto"=>$pacchetto["codice"]));
        if ($ris_files->rowCount() > 0) {
          ?>alert('Cancellazione effettuata con successo.')
          window.location.href=window.location.href;<?
        } else {
          ?>alert('Si sono verificati degli errori durante la cancellazione.')<?
        }
      } else {
        ?>alert('Non si dispone dei permessi necessari.')<?
        die();
      }
    } else {
      ?>alert('Non si dispone dei permessi necessari.')<?
      die();
    }
  }
?>
