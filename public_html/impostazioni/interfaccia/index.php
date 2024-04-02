<?
	include_once("../../../config.php");
	include_once($root."/layout/top.php");

	$edit = false;
	if (isset($_SESSION["codice_utente"]) && isset($_SESSION["ente"])) {
		$edit = check_permessi("impostazioni",$_SESSION["codice_utente"]);
		if (!$edit) {
			echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
			die();
		}
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}
	if ($edit) {
		$strsql= "SELECT * FROM b_interfaccia WHERE codice_ente = :codice_ente";
		$risultato = $pdo->bindAndExec($strsql,array(":codice_ente"=>$_SESSION["ente"]["codice"]));
		if ($risultato->rowCount()>0) {
			$record_interfaccia = $risultato->fetch(PDO::FETCH_ASSOC);
		} else {
			$record_interfaccia = get_campi("b_interfaccia");
		}
		?>
    <form name="box" method="post" action="save.php" rel="validate" >
      <div class="comandi">
        <button class='btn-round btn-primary' title="Salva"><span class="fa fa-floppy-o"></span></button>
      </div>
      <input type="hidden" id="codice" name="codice" value="<? echo $record_ente["codice"]; ?>">
      <div id="interfaccia">
        <h1>Personalizza interfaccia</h1>
        <input type="button" class="submit_big" style="background-color:#F30" value="Rimuovi tutto" onClick="$('.rimuovi_colore').trigger('click');">
        <input type="hidden" name="interfaccia[a]" id="a" value="<? echo $record_interfaccia["a"] ?>">
        <input type="hidden" name="interfaccia[a_hover]" id="a_hover" value="<? echo $record_interfaccia["a_hover"] ?>">
        <input type="hidden" name="interfaccia[menu]" id="interfaccia_menu" value="<? echo $record_interfaccia["menu"] ?>">
        <input type="hidden" name="interfaccia[menu_background_a]" id="menu_background_a" value="<? echo $record_interfaccia["menu_background_a"] ?>">
        <input type="hidden" name="interfaccia[menu_background_a_hover]" id="menu_background_a_hover" value="<? echo $record_interfaccia["menu_background_a_hover"] ?>">
        <input type="hidden" name="interfaccia[menu_color_a]" id="menu_color_a" value="<? echo $record_interfaccia["menu_color_a"] ?>">
        <input type="hidden" name="interfaccia[menu_color_a_hover]" id="menu_color_a_hover" value="<? echo $record_interfaccia["menu_color_a_hover"] ?>">
        <input type="hidden" name="interfaccia[menu_moduli_background_a]" id="menu_moduli_background_a" value="<? echo $record_interfaccia["menu_moduli_background_a"] ?>">
        <input type="hidden" name="interfaccia[menu_moduli_background_a_hover]" id="menu_moduli_background_a_hover" value="<? echo $record_interfaccia["menu_moduli_background_a_hover"] ?>">
        <input type="hidden" name="interfaccia[menu_moduli_color_a]" id="menu_moduli_color_a" value="<? echo $record_interfaccia["menu_moduli_color_a"] ?>">
        <input type="hidden" name="interfaccia[menu_moduli_color_a_hover]" id="menu_moduli_color_a_hover" value="<? echo $record_interfaccia["menu_moduli_color_a_hover"] ?>">
        <input type="hidden" name="interfaccia[utente_background_a]" id="utente_background_a" value="<? echo $record_interfaccia["utente_background_a"] ?>">
        <input type="hidden" name="interfaccia[utente_background_a_hover]" id="utente_background_a_hover" value="<? echo $record_interfaccia["utente_background_a_hover"] ?>">
        <input type="hidden" name="interfaccia[utente_color_a]" id="utente_color_a" value="<? echo $record_interfaccia["utente_color_a"] ?>">
        <input type="hidden" name="interfaccia[utente_color_a_hover]" id="utente_color_a_hover" value="<? echo $record_interfaccia["utente_color_a_hover"] ?>">
        <input type="hidden" name="interfaccia[bottom_color]" id="bottom_color" value="<? echo $record_interfaccia["bottom_color"] ?>">
				<input type="hidden" name="interfaccia[bottom]" id="interfaccia_bottom" value="<? echo $record_interfaccia["bottom"] ?>">
        <input type="hidden" name="interfaccia[bottom_a]" id="interfaccia_bottom_a" value="<? echo $record_interfaccia["bottom_a"] ?>">
        <input type="hidden" name="interfaccia[menu_top]" id="menu_top" value="<? echo $record_interfaccia["menu_top"] ?>">
        <input type="hidden" name="interfaccia[menu_top_a]" id="menu_top_a" value="<? echo $record_interfaccia["menu_top_a"] ?>">
				<input type="hidden" name="interfaccia[menu_active_border]" id="menu_active_border" value="<? echo $record_interfaccia["menu_active_border"] ?>">
        <input type="hidden" name="interfaccia[menu_active_background]" id="menu_active_background" value="<? echo $record_interfaccia["menu_active_background"] ?>">
        <table width="100%">
        	<tr>
            <td colspan="3"><h2>Collegamenti</h2></td>
          </tr>
          <tr>
            <td class="etichetta">
              <h3>Testo</h3>
            </td>
            <td <? if ($record_interfaccia["a"]!="") { ?> style="background:#<? echo $record_interfaccia["a"] ?>"<? } ?> rel="a" object="a" property="color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              <h3>Testo su selezione</h3>
            </td>
            <td <? if ($record_interfaccia["a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["a_hover"] ?>"<? } ?> rel="a_hover" object="a:hover" property="color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
          <tr>
            <td colspan="3" class="etichetta">
              <h2>Menu</h2>
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              <h3>Menu Laterale</h3>
            </td>
            <td <? if ($record_interfaccia["menu"]!="") { ?> style="background:#<? echo $record_interfaccia["menu"] ?>"<? } ?> rel="interfaccia_menu" object="#menu" property="background-color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              <h4>Voci selezionate - Bordo laterale</h4>
            </td>
            <td <? if ($record_interfaccia["menu_active_border"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_active_border"] ?>"<? } ?> rel="menu_active_border" object="#menu_active_border" property="border-color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
					<tr>
            <td class="etichetta">
              <h4>Voci selezionate - Sfondo</h4>
            </td>
            <td <? if ($record_interfaccia["menu_active_background"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_active_background"] ?>"<? } ?> rel="menu_active_background" object="#menu_active_background" property="background-color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              <h3>Menu Superiore</h3>
            </td>
            <td <? if ($record_interfaccia["menu_top"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_top"] ?>"<? } ?> rel="menu_top" object="#menu-top" property="background-color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
          <tr>
            <td class="etichetta">
              <h3>Testo Menu Superiore</h3>
            </td>
            <td <? if ($record_interfaccia["menu_top_a"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_top_a"] ?>"<? } ?> rel="menu_top_a" object="#menu-top a, #menu-top .right li" property="color" class="color_selector"></td>
            <td width="10">
              <input type="button" class="rimuovi_colore" value="Rimuovi">
            </td>
          </tr>
        </table>

								<table width="100%">
								<tr>
                                <td colspan="6" class="etichetta"><h2>Collegamenti</h2></td></tr>
                                <tr>
                                <td class="etichetta">Sfondo</td>
                                <td <? if ($record_interfaccia["menu_background_a"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_background_a"] ?>"<? } ?> rel="menu_background_a" object="#list_menu li" property="background-color" class="color_selector"></td>
                                <td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo</td><td rel="menu_color_a" object="#list_menu li a, .descr_ente h2, .descr_ente a, .info_add" property="color"  class="color_selector" <? if ($record_interfaccia["menu_color_a"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_color_a"] ?>"<? } ?>></td>
                                <td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                <tr>
                                <td class="etichetta">Sfondo su selezione</td><td rel="menu_background_a_hover" object="#list_menu li a:hover" property="background-color" class="color_selector" <? if ($record_interfaccia["menu_background_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_background_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo su selezione</td><td rel="menu_color_a_hover" object="#list_menu li a:hover" property="color"  class="color_selector" <? if ($record_interfaccia["menu_color_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_color_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                <tr><td colspan="6" class="etichetta"><h2>Moduli</h2></td></tr>
                                <tr>
                                <td class="etichetta">Sfondo</td><td rel="menu_moduli_background_a" object="#menu_moduli li" property="background-color" class="color_selector" <? if ($record_interfaccia["menu_moduli_background_a"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_moduli_background_a"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo</td><td rel="menu_moduli_color_a" object="#menu_moduli li a" property="color"  class="color_selector" <? if ($record_interfaccia["menu_moduli_color_a"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_moduli_color_a"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                <tr>
                                <td class="etichetta">Sfondo su selezione</td><td rel="menu_moduli_background_a_hover" object="#menu_moduli li a:hover" property="background-color" class="color_selector" <? if ($record_interfaccia["menu_moduli_background_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_moduli_background_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo su selezione</td><td rel="menu_moduli_color_a_hover" object="#menu_moduli li a:hover" property="color"  class="color_selector" <? if ($record_interfaccia["menu_moduli_color_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["menu_moduli_color_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                <tr><td colspan="6" class="etichetta"><h2>Utente</h2></td></tr>
                                <tr>
                                <td class="etichetta">Sfondo</td><td rel="utente_background_a" object="#utente li" property="background-color" class="color_selector" <? if ($record_interfaccia["utente_background_a"]!="") { ?> style="background:#<? echo $record_interfaccia["utente_background_a"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo</td><td rel="utente_color_a" object="#utente li a" property="color"  class="color_selector" <? if ($record_interfaccia["utente_color_a"]!="") { ?> style="background:#<? echo $record_interfaccia["utente_color_a"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                <tr>
                                <td class="etichetta">Sfondo su selezione</td><td rel="utente_background_a_hover" object="#utente li a:hover" property="background-color" class="color_selector" <? if ($record_interfaccia["utente_background_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["utente_background_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo su selezione</td><td rel="utente_color_a_hover" object="#utente li a:hover" property="color"  class="color_selector" <? if ($record_interfaccia["utente_color_a_hover"]!="") { ?> style="background:#<? echo $record_interfaccia["utente_color_a_hover"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
                                </table>
                                <table width="100%">
                                <tr>
                                <td colspan="6"><h2>Barra inferiore</h2></td>
                                </tr>
                                 <tr>
                                <td class="etichetta">Sfondo</td><td rel="interfaccia_bottom" object="body" property="background-color" class="color_selector" <? if ($record_interfaccia["bottom"]!="") { ?> style="background:#<? echo $record_interfaccia["bottom"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
								<td class="etichetta">Testo</td><td rel="bottom_color" object="#bottom" property="color"  class="color_selector" <? if ($record_interfaccia["bottom_color"]!="") { ?> style="background:#<? echo $record_interfaccia["bottom_color"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
                            	</tr>
														 <tr>
															<td class="etichetta">Collegamenti</td><td rel="interfaccia_bottom_a" object="#bottom a" property="color" class="color_selector" <? if ($record_interfaccia["bottom_a"]!="") { ?> style="background:#<? echo $record_interfaccia["bottom_a"] ?>"<? } ?>></td><td width="10"><input type="button" class="rimuovi_colore" value="Rimuovi"></td>
														</tr>
                            </table>
                        </div>
               <input type="submit" class="submit_big" value="Salva">
	</form>
        <?
	} else {
		echo '<meta http-equiv="refresh" content="0;URL=/index.php">';
		die();
	}

	include_once($root."/layout/bottom.php");
	?>
