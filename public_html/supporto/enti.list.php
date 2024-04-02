<?
	session_start();
	session_write_close();
	include_once '../../config.php';
  	include_once "{$root}/inc/funzioni.php";

	if(! empty($_SESSION["codice_utente"])) {
    	if (check_permessi("supporto",$_SESSION["codice_utente"]) && in_array($_SESSION["tipo_utente"], array('SAD', 'SUP'))) {
    		if(! empty($_POST["user_id"])) {
    			$bind = array(":codice_utente" => $_POST["user_id"]);
    			$sql = "SELECT b_enti.codice, b_enti.denominazione FROM b_enti JOIN r_enti_operatori ON r_enti_operatori.cod_ente = b_enti.codice WHERE r_enti_operatori.cod_utente = :codice_utente";
    			$ris = $pdo->bindAndExec($sql, $bind);
    			if ($ris->rowCount()>0) {
    				?>
    				<div style="max-height:500px; overflow:scroll">
	    				<?
    					while($rec = $ris->fetch(PDO::FETCH_ASSOC)) {
		    			  	?>
							<button onClick="<?= $_POST["function"] ?>(<?= $_POST["user_id"] ?>, <?= $rec["codice"] ?>);" class="box" style="display:block; width:100%; border:0; cursor:pointer; text-align:left">
								<i class="fa fa-university"></i> <?= $rec["denominazione"] ?>
							</button>
		    			  	<?
	    			  	}
		    			?>
    				</div>
    				<?
    				return;
    			}
    		}
    	}
	}
	header("HTTP/1.1 500 Internal Server Error");
?>