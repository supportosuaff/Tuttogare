<?
	die();
	/* if ( isset($codice_gara) && isset($token) && isset($destinatario) && isset($password ))
	{
		$errore = "";
		$errore_mail = false;
		$bind = array();
		$bind[":codice"] = $codice_gara;
		$sql_gara = "SELECT `b_gare`.`oggetto`, `b_gare`.`codice_pec` FROM `b_gare` WHERE `b_gare`.`codice` = :codice";
		$ris_gara = $pdo->bindAndExec($sql_gara,$bind);
		if ($ris_gara->rowCount() > 0)
		{
			$rec_gara = $ris_gara->fetch(PDO::FETCH_ASSOC);
			$url = $config["protocollo"] . $_SESSION["ente"]["dominio"] . "/pannello-commissione/login.php?token=" . $token . "&codice=" . $codice_gara;
			$oggetto = "Commissario di Gara #" . $codice_gara . " - " . $rec_gara["oggetto"];
			$corpo = "Credenziali di Accesso al portale per la valutazione dei Partecipanti<br>";
			$corpo.= 'URL: <a href="'.$url.'">'.$url.'</a><br><br>PASSWORD: <b>'.$password.'</b>';

			$mailer = new Communicator();
			$mailer->oggetto = $oggetto;
			$mailer->corpo = $corpo;
			$mailer->codice_pec = -1;
			$mailer->comunicazione = false;
			$mailer->coda = false;
			$mailer->destinatari = $destinatario;

			if($mailer->send() !== true){
				$errore_mail = true;
				$errore .= "Problema durante l'invio.\n";
				echo "jalert('Non è stato possibile inviare la comunicazione.<br>Verificare indirizzo PEC. $destinatario');";
			}
		}
	} else {
		 header('Location: /index.php');
	} */
?>
