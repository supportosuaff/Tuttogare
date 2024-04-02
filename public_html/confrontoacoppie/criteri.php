<?
if ($ris_criteri->rowCount() > 0)
{
	$i = 0;
	?>
	<h2 style="cursor:pointer" onclick="toggle_valutazione()">ELEMENTI DI VALUTAZIONE TECNICA <img height="16" src="/img/arrowDown.png"></h2>
	<table id="elementi_valutazione" class="no_border"<? if ($valutazione) echo 'style="display: none"' ?> width="100%">
		<thead>
			<tr class="macro">
				<td width="10%">&nbsp;</td>
				<td width="80%" colspan="2">&nbsp;</td>
				<td width="10%" align="center">Peso</td>
			</tr>
		</thead>
		<tbody>
			<?
			while ($rec_criteri = $ris_criteri->fetch(PDO::FETCH_ASSOC))
			{
				$i++;
				?>
				<tr class="macro">
					<td width="10%" >EVT.<?= $i ?></td>
					<td width="80%" colspan="2"><?= $rec_criteri["descrizione"] ?></td>
					<td width="10%" align="center"><?= $rec_criteri["punteggio"] ?></td>
				</tr>
				<?
				$bind = array();
				$bind[":codice_gara"] = $codice_gara;
				$bind[":codice_padre"] = $rec_criteri["codice"];
				$sql_sub_criteri = "SELECT * FROM b_valutazione_tecnica WHERE codice_gara = :codice_gara AND codice_padre = :codice_padre";
				$ris_sub_criteri = $pdo->bindAndExec($sql_sub_criteri,$bind);
				if ($ris_sub_criteri->rowCount() > 0)
				{
					$j = 0;
					while ($rec_sub_criteri = $ris_sub_criteri->fetch(PDO::FETCH_ASSOC))
					{
						$j++;
						?>
						<tr>
							<td width="10%">&nbsp;</td>
							<td width="10%">EVT.<?= $i.".".$j ?></td>
							<td width="70%"><?= $rec_sub_criteri["descrizione"] ?></td>
							<td width="10%" align="center"><?= $rec_sub_criteri["punteggio"] ?></td>
						</tr>
						<?
						$criteri[$i][$j] = $rec_sub_criteri["codice"];
					}
				}
				else
				{
					$criteri[$i] = $rec_criteri["codice"];
				}
			}
			?>
		</tbody>
	</table>
	<?
} else {
  $edit = false;
  unset($_SESSION["codice_commissario"]);
  unset($_SESSION["token"]);
  unset($_SESSION["commissario"]);
  $errore = true;
  ?>
  <div id="info" class="ui-state-error padding">
    <h2>Errore di configurazione</h2>
    <p>Attenzione, questa procedura di gara non dispone di criteri per la valutazione a coppie. Si prega di contattare l&#39;amministrazione.</p>
  </div>
  <?
  die();
}
?>
