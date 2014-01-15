<?if(count($msg)){?>
	<table width="100%">
	<?=parse_msg_array($msg,'blank',1,0,'small')?>
	</table>
<?}?>
<div style="margin:10px;">
<div style="font-size:130%;font-weight:bold;">
Lehrveranstaltungsanteile der Dozenten für die Lehrabrechnung
</div>
<div style="font-size:80%;margin:5px;">
Für die Erstellung der Erhebungsbögen zur Lehrverpflichtungsverordnung wird der Anteil der einzelnen Dozenten an der Lehrveranstaltung berücksichtigt.<br>
Sie haben in dieser Eingabemaske die Möglichkeit, diesen Anteil per Hand festzusetzen. Dafür geben sie zuerst die Anzahl der SWS in das entsprechende Feld ein. Anschließend tragen sie für jeden Dozenten die anteiligen SWS ein (maximal 2 Nachkommastellen).<br>
Eine automatische Prüfung der Eingabe erfolgt nicht. Damit sind auch Eingaben möglich, in denen die Summe der einzelnen Anteile über den Gesamt-SWS liegen. Dies ermöglicht bspw. die richtige Behandlung bei Einzelkursen und mehreren Dozenten.<br>
Werden die Anteile nicht eingetragen, erfolgt die Berechnung wie bisher mit der Annahme einer Gleichverteilung.
</div>

<form action="<?=PluginEngine::getLink($plugin) ?>" method="POST">
<div style="font-size:80%;font-weight:bold;">
<label for="sws_seminar">Stundenzahl der Lehrveranstaltung in SWS:</label>
<input type="text" id="sws_seminar" name="sws_seminar" size="4" maxlength="4" value="<?=($sws_seminar  ? round($sws_seminar,2) : '')?>">
</div>
<div style="font-size:80%;font-weight:bold;margin-top:5px;">
Lehrveranstaltungsanteile der einzelnen Dozenten in SWS:
</div>
<table cellspacing="2" cellpadding="2" border="0" width="600">
<tr style="font-size:80%">
<th width="30%"><?=_("Nachname")?></th>
<th width="30%"><?=_("Vorname")?></th>
<th width="5%"><?=_("SWS")?></th>
<th width="25%"><?=_("Bearbeiter")?></th>

</tr>
<?
$cssSw = new cssClassSwitcher();
foreach($plugin->seminar->getMembers('dozent') + $plugin->seminar->getMembers('tutor') as $user_id => $user_data){
	$cssSw->switchClass();
	$class = $cssSw->getClass();
	?>
	<tr style="font-size:80%">
	<td class="<?=$class?>"><?=htmlReady($user_data['Nachname'])?></td>
	<td class="<?=$class?>"><?=htmlReady($user_data['Vorname'])?></td>
	<td class="<?=$class?>" align="center">
	<input type="text" name="sws_user[<?=$user_id?>]" size="4" maxlength="4" value="<?=($lvvo_data[$user_id]['sws_user'] ? round($lvvo_data[$user_id]['sws_user'],2) : '')?>">
	</td>
	<td class="<?=$class?>">
	<?=($lvvo_data[$user_id]['chdate'] ? htmlReady(get_fullname($lvvo_data[$user_id]['last_changed_user_id'],'no_title_short') . ' ' . strftime("%x", $lvvo_data[$user_id]['chdate'])) : '&nbsp;')?></td>
	</tr>
<?}?>
</table>
<div style="margin-top:10px;">
	<?= makeButton('uebernehmen', 'input', _("Eingaben abspeichern"), 'save') ?>
	&nbsp;
    <a href="<?=PluginEngine::getLink($plugin)?>">
	<?= makeButton('abbrechen', 'img', _("Eingabe abbrechen"), 'cancel') ?>
    </a>
</div>
</form>
</div>

