<? if (isset($_POST["id"])) {
		session_start();
		include("../../../config.php");
		include_once($root."/inc/funzioni.php");
;
		$record_pec = get_campi("b_pec");
		$id = $_POST["id"];
		$new = true;
	}
?>
<div class="box" id="<? echo $id ?>">
						<input type="hidden" name="pec[<? echo $id ?>][codice]" id="codice_<? echo $id ?>" value="<? echo $id ?>">
                        <table width="100%">
                        <tr><td class="etichetta">PEC*</td><td><input class="titolo_edit" autocomplete="off" type="text" name="pec[<? echo $id ?>][pec]" id="pec_<? echo $id ?>" title="pec" value="<? echo $record_pec["pec"] ?>" rel="S;0;0;E"></td>
                        <td class="etichetta">Password*</td><td><input class="titolo_edit" autocomplete="off" type="password" name="pec[<? echo $id ?>][password]" id="password_<? echo $id ?>" title="password" value="<? if ($record_pec["password"] != "") echo simple_decrypt($record_pec["password"],$_SESSION["ente"]["cf"]) ?>" rel="S;0;0;A;"></td>
						 <td rowspan="3" width="10"><input type="image" onClick="elimina('<? echo $id ?>','impostazioni/pec');return false;" src="/img/del.png" title="Elimina"></td>
                        </td>
                        </tr>
                        <tr>
                        	<td class="etichetta">SMTP*</td>
                            <td><input type="text" name="pec[<? echo $id ?>][smtp]" id="smtp_<? echo $id ?>" title="smtp" value="<? echo $record_pec["smtp"] ?>" rel="S;0;0;A"></td>
                        	<td class="etichetta">Porta</td><td><input type="text" name="pec[<? echo $id ?>][smtp_port]" id="port_<? echo $id ?>" title="Porta" size="4" value="<? echo $record_pec["smtp_port"] ?>" rel="S;0;0;N;"></td></tr>
                            <tr>
                                <tr>
								<td class="etichetta">IMAP</td>
								<td><input type="text" name="pec[<? echo $id ?>][imap]" id="imap_<? echo $id ?>" title="imap" value="<? echo $record_pec["imap"] ?>" rel="N;0;0;A"></td>
								<td class="etichetta">Porta</td><td><input type="text" name="pec[<? echo $id ?>][imap_port]" id="imap_port_<? echo $id ?>" title="Porta" size="4" value="<? echo $record_pec["imap_port"] ?>" rel="N;0;0;N;"></td></tr>
							<tr>
                            <td class="etichetta">SSL</td><td>
    							<input type="checkbox" name="pec[<? echo $id ?>][usa_ssl]" id="usa_ssl_<? echo $id ?>" <? if ($record_pec["usa_ssl"]) echo "checked" ?>>
                            </td><td colspan="2"><input style="width:100%;background:#F60;" type="button" class="submit" value="Prova" onClick="prova_configurazione('<? echo $id ?>');return false;"></td>
                        </tr>
                        </table>
                       </div>
