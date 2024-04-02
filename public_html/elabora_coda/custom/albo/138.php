<?php

ob_start();
?>
Si informa la S.V. che il <?= mysql2date($scadenza) ?> scadr&agrave; l'abilitazione relativa al bando:<br><br>
<strong><?=  $bando["oggetto"] ?></strong><br><br>
Ricordando che il Modello A per l&rsquo;iscrizione al Sistema di Qualificazione,
istituito da Acqualatina S.p.A. ex art. 134 del D.Lgs. 50/2016, ha validit&agrave; di sei (6) mesi
trattandosi di una dichiarazione sostitutiva ai sensi degli artt. 19, 19 bis, 38, 46, 47, 48 e 77 bis del D.P.R. 445/200 e s.m.i.,
la invitiamo ad aggiornare quanto prima i suoi dati collegandosi al link: <br>
<a href="<?= $href ?>" title="Sito esterno"><?= $href ?></a><br><br>
Distinti Saluti<br><br>
<?
$corpo = ob_get_clean();
