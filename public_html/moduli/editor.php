<?
	if (isset($_SESSION["codice_utente"]) && isset($html)) {
		if (!isset($file_title)) $file_title = "File";
		if (!isset($orientamento)) $orientamento = "P";
		if (!isset($formato)) $formato = "A4";
        if (isset($editor_tipo)) {
            if (!isset($sezione_documentale)) $sezione_documentale = "gara";
            if (!isset($record_gara)) $record_gara = $record_bando;
						$bind = array();
						$bind[":tipo"] = $editor_tipo;
						$bind[":sezione"] = $sezione_documentale;
						$bind[":codice_gara"] = $record_gara["codice"];
            $sql_history = "SELECT * FROM b_documentale WHERE tipo=:tipo AND attivo = 'N' AND sezione = :sezione AND codice_gara = :codice_gara ORDER BY timestamp DESC";
            $ris_history = $pdo->bindAndExec($sql_history,$bind);
						if ($ris_history->rowCount()>0) {
                ?>
                <div id="history" style="display: none;">
                        <? while ($h_modello = $ris_history->fetch(PDO::FETCH_ASSOC)) {
                            echo "<button class='submit_big' onClick='versione(" . $h_modello["codice"] . ",\"Versione " . mysql2completedate($h_modello["timestamp"]) . "\");return false;'><span class=\"fa fa-search\"\" style=\"vertical-align:middle\" alt=\"Visualizza versione\"></span> " . mysql2completedate($h_modello["timestamp"]) . "</button>";
                        } ?>
                </div>
                <?
            }
        }
		?>
        <div id="editor">
        <div style="text-align:right">
            <?
                 if (isset($ris_history) && ($ris_history->rowCount()>0)) {
                    ?>
                    <button class="submit espandi" onClick="$('#history').dialog({ modal:'true',title:'Versioni'});return false;"><span class="fa fa-search"></span> Archivio versioni</button>
                    <?
                 }
            ?>
    	    <button class="submit espandi" onClick="exportPDF();return false;"><img src="/img/pdf.png" style="vertical-align:middle" alt="Esporta in PDF"> Esporta in PDF</button>
        </div>
        <textarea id="corpo" rel="S;0;0;A" title="Corpo" name="corpo" class="ckeditor_models"><? echo $html; ?></textarea>
        	<input type="hidden" name="file_title" id="file_title" value="<? echo $file_title ?>">
        	<input type="hidden" name="orientamento" id="orientamento" value="<? echo $orientamento ?>">
					<input type="hidden" name="formato" id="formato" value="<? echo $formato ?>">
        </div>
        <?
	}
?>
