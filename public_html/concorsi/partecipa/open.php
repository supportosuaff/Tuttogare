<?
	session_start();
	ini_set('max_execution_time', 600);
	ini_set('memory_limit', '-1');
	include_once("../../../config.php");
	include_once($root."/inc/funzioni.php");
	if (is_operatore() && !empty($_POST["codice_gara"]) && !empty($_POST["codice_busta"]) && !empty($_POST["salt"])) {

			$codice_gara = $_POST["codice_gara"];

			$bind = array();
			$bind[":codice"] = $codice_gara;
			$bind[":codice_ente"] = $_SESSION["ente"]["codice"];
			$strsql  = "SELECT b_concorsi.* FROM b_concorsi
									WHERE b_concorsi.codice = :codice ";
			$strsql .= "AND b_concorsi.annullata = 'N' ";
			$strsql .= "AND codice_gestore = :codice_ente ";
			$strsql .= "AND (pubblica = '2' OR pubblica = '1') ";
			$risultato = $pdo->bindAndExec($strsql,$bind);

			if ($risultato->rowCount() > 0) {

				$record_gara = $risultato->fetch(PDO::FETCH_ASSOC);

				$fase_attiva = array();

				$sql_fasi = "SELECT * FROM b_fasi_concorsi WHERE codice_concorso = :codice_gara  ";
				$ris_fasi = $pdo->bindAndExec($sql_fasi,array(":codice_gara"=>$record_gara["codice"]));
				if ($ris_fasi->rowCount() > 0) {
					while($fase = $ris_fasi->fetch(PDO::FETCH_ASSOC)) {
						if ($fase["attiva"]=="S") {
							$fase_attiva = $fase;
						}
					}
				}

				if (!empty($fase_attiva) && !empty($_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]]["salt"])) {
					$partecipante = $_SESSION["concorsi"][$record_gara["codice"]][$fase_attiva["codice"]];

					$bind = array();
					$bind[":codice_gara"] = $_POST["codice_gara"];
					$bind[":codice_busta"] = $_POST["codice_busta"];
					$bind[":codice_fase"] = $fase_attiva["codice"];
					$bind[":codice_utente"] = $partecipante["codice"];
					$strsql  = "SELECT b_buste_concorsi.* FROM b_buste_concorsi JOIN r_partecipanti_concorsi ON b_buste_concorsi.codice_partecipante = r_partecipanti_concorsi.codice
											WHERE b_buste_concorsi.codice_gara = :codice_gara AND b_buste_concorsi.codice_fase = :codice_fase AND r_partecipanti_concorsi.codice = :codice_utente
											AND b_buste_concorsi.codice_busta = :codice_busta ORDER BY b_buste_concorsi.codice DESC";
			$risultato = $pdo->bindAndExec($strsql,$bind);
			if ($risultato->rowCount() > 0) {
				$busta = $risultato->fetch(PDO::FETCH_ASSOC);
				if ($busta["aperto"]=="N") {
					$enc_data = file_get_contents($config["doc_folder"] . "/concorsi/" . $busta["codice_gara"] . "/" . $busta["codice_fase"] . "/" . $busta["nome_file"]);
					$data = openssl_decrypt($enc_data,$config["crypt_alg"],$_POST["salt"],OPENSSL_RAW_DATA,$config["enc_salt"]);
					if ($data !== false) {
						$estensione = "";
						$tmp_file = $config["chunk_folder"] . "/" . session_id() . ".tmp";
						file_put_contents($tmp_file,$data);
						$type = getTypeAndExtension($tmp_file);
						$estensione =  $type["ext"];
						$type = $type["type"];
				    unlink($tmp_file);
					}
				} else if ($busta["aperto"]=="S") {
					$bind = array();
					$bind[":codice"] = $busta["codice_allegato"];
					$strsql = "SELECT b_allegati.* FROM b_allegati WHERE b_allegati.codice = :codice ";
					$risultato = $pdo->bindAndExec($strsql,$bind);
					if ($risultato->rowCount() > 0) {
						$record_allegato = $risultato->fetch(PDO::FETCH_ASSOC);
						$type = getTypeAndExtension($config["arch_folder"] . "/concorsi/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
						$estensione =  $type["ext"];
						$type = $type["type"];
						$data = file_get_contents($config["arch_folder"] . "/concorsi/" . $record_allegato["codice_gara"] . "/" . $record_allegato["cartella"] . "/" . $record_allegato["riferimento"]);
					}
				}
				if (!empty($data)) {
					header('Content-Description: File Transfer');
					header('Content-Type: '.$type);
					header('Content-Disposition: attachment; filename=Documentazione'.$estensione);
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					echo $data;
				} else {
					?>
					<h1>Pagina non trovata o privilegi insufficienti 4</h1>
					<?
				}
			} else {
				?>
				<h1>Pagina non trovata o privilegi insufficienti 3</h1>
				<?
			}
		} else {
			?>
			<h1>Pagina non trovata o privilegi insufficienti 2</h1>
			<?
		}
	} else {
		?>
		<h1>Pagina non trovata o privilegi insufficienti 1</h1>
		<?
	}
} else {
	?>
	<h1>Pagina non trovata o privilegi insufficienti 0</h1>
	<?
}
?>
