<?php
	// Alle GET-Parameter in $var speichern
	$addonurl		= rex_request('addonurl', 'string');
	$addonname		= rex_request('addonname', 'string');
	$addonid		= rex_request('addonid', 'int');
	$func			= rex_request('func', 'string');
	$addonfile		= rex_request('addonfile','string');
	$installname		= rex_request('installname', 'string');
	
	// Für Settings
	$addon_text_versions = "";
	
	if($addonid != "")
	{
		
		// Funktion aufrufen, um JSON von Redaxo-API zurückzubekommen
		// Rufe Session ab, welche bereits auf der addons.inc.php gespeichert wurde
		$addons = $_SESSION['addonsAsObject'];
    
    $addons = Installer_array_sort($addons, 'file_version', SORT_DESC); // Sort by oldest first
?>
	<div class="rex-addon-output">
		<h2 class="rex-hl2">Verfügbare Downloads
            <div class="action_bar_wrapper">
                <a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            </div>
		</h2>
			<?php 
				
				echo rex_info('Bitte wähle jetzt ein Paket.<br />Alle hier gezeigten Downloads sind laut Entwickler kompatibel mit REX '.rex_session('userexversion').'. Du nutzt Version '.$REX['VERSION'].'.'.$REX['SUBVERSION'].'. Bitte beachte zusätzlich alle Entwicklerhinweise, bevor du ein AddOn installierst.');

				if(Installer_check_if_addon_exists($REX['INCLUDE_PATH']."/addons/".$installname))
				{
					echo rex_warning('Achtung: Das Addon existiert bereits auf Deinem System. Wenn du die Installation fortführst, werden alle Daten von diesem AddOn überschrieben. Sollte es sich hierbei um ein Update des AddOns handeln, musst du dieses eventuell im Anschluss "re-installieren". Dies ist normalerweise nur notwendig, wenn Datenbankabhängigkeiten vom Addon aus bestehen. Bitte lege sicherheitshalber ein Backup des AddOns und der Datenbank an.');
				}
				
				// Debug only
				//print_r($addons);
			?>
		<div class="rex-addon-content">
			<p class="rex-tx1">
            
				<?php
					// Zähle Ergebnisse in Array
					$ergebnis = sizeof($addons);
					
					if($ergebnis > 0)
					{
						echo '
						<table class="rex-table install-modal">
							<tr>
								<!--<th>Addon</th>-->
								<th>Version</th>
								<th>Info</th>
								<th>Beschreibung</th>
								<th>Uploaddatum</th>
								<th>Check</th>
								<th>Aktion</th>
							</tr>';
						foreach($addons as $addon)
						{
							// Wir Filtern nach addon_id
							if($addon->addon_id == $addonid)
							{
							  //print_r($addon);
								// Prüft auf die Schnelle, ob ein Setup möglich ist und gibt Pre-Info raus
								// Ein Klick auf das Plus-Zeichen liefert wetere Ergebnisse
								if(Installer_fast_file_check($addon->file_path, $addon->addon_key, $REX['INCLUDE_PATH']))
								{
									$installable = '<img src="media/addons/installer/checkmark.png" title="Fastcheck bestanden. AddOn sollte sich ohne Probleme installieren lassen!" alt="Fastcheck bestanden" />';	
									
								} else {
									
									$installable = '<img src="media/addons/installer/warning.gif" title="Im Fastcheck durchgefallen. Klick auf das Plus-Zeichen, um weitere Informationen zu erhalten" alt="Im Fastcheck durchgefallen!" />';	
								}
								
								
								$dateTime = Installer_timestamp_convert($addon->file_created);
								
								$addon_text_versions = ($addon->file_description ? $addon->file_description : $addon->addon_description);
								
								if($REX["ADDON"]["installer"]["settings"]["SELECT"]["linkconvert_versions"]) {
									$addon_text_versions = Installer_url_to_link(strip_tags($addon_text_versions));
								}
			
								if($REX["ADDON"]["installer"]["settings"]["SELECT"]["nl2br_versions"]) {
									$addon_text_versions = nl2br(strip_tags($addon_text_versions));
								}
								
								echo '
									<tr>
										<!--<td>' . $addon->addon_name . '</td>-->
										<td>' . $addon->file_version . '</td>
										<td>' . $addon->file_name . '</td>
										<td>' . $addon_text_versions . '</td>
										<td>' . $dateTime['date'] . ' um ' . $dateTime['time'] .'</td>
										<td>'.$installable.'</td>
										<td><a href="#" class="install-addon-link" data-addon="?page=installer&subpage=addon_installer&pluginpage=install&func=curl&addonfile=' . $addon->file_path . '&installname='.urlencode($addon->addon_key).'&addonname='.urlencode($addon->addon_name).'"><img src="media/file_add.gif" title="Installieren" alt="Installieren"/></a></td>
									</tr>';
							}
							
						}
					} else {
						echo rex_warning('Leider hat der Ersteller dieses Addons keine Dateien zum Download bereitgestellt oder diese in der Zwischenzeit gelöscht');
					}
					
				?>
				</table>
			</p>
		</div>
	</div>

<?php
}
?>

<?php
if($func == "curl")
{
	// Wir prüfen den Rückgabewert der function, true führt unzip aus
	if(Installer_download_file_from_redaxo($addonfile, $REX['INCLUDE_PATH']."/addons/installer/temp/temp.zip"))
	{
		if(Installer_unzip_file_to_addon_dir($addonDir."/".$tmpDir."/".$tmpFile, $REX['INCLUDE_PATH'], $installname) == true)
		{
		    
		    if($installname == "installer")
    		{
    		    $success_msg = rex_info('Der Installer wurde geupdated. Der Re-Install wurde automatisch durchgeführt. Es sind keine weiteren Aktionen notwendig.');
    			$success_msg .= '<span class="force_reinstall" style="display:none"></span>';
    		} else {
    		    $success_msg = rex_info('Das AddOn "'.$addonname.'" wurde soeben geladen, entpackt und steht nun unter <a href="?page=addon">AddOn</a> zur Verfügung. Damit Du das neue AddOn nutzen kannst, musst Du es jetzt oder später installieren und aktivieren.');
    		}
    		
			echo '
				<div class="rex-addon-output">
					<h2 class="rex-hl2">Installation abgeschlossen</h2>
				    <div class="action_bar_wrapper">
				    	<a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            		</div>
					'.$success_msg.'
			        <ul class="action_after_unzip">
			            <li><a href="#" data-installname="'.str_replace('/', '', $installname).'" class="zip_install_activate">AddOn (re-)installieren und gleichzeitig aktivieren</a></li>
			            <li><a href="#" data-installname="'.str_replace('/', '', $installname).'" class="zip_install">AddOn nur (re-)installieren</a></li>
			            <li><a href="?page=addon">Ins AddOn-Verzeichnis wechseln</a></li>
			      		<li><a href="#" class="closebox">Beenden</a></li>
			        </ul>
				</div>';
				
				// Funktion zum löschen des AddonCache aufrufen.
				//Installer_clearSessionCache();
		} else {
			echo '
				<div class="rex-addon-output">
					<h2 class="rex-hl2">Installation abgebrochen</h2>
					<div class="action_bar_wrapper">
				    	<a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            		</div>
					'.rex_warning('Installer hat den Setup-Prozess des AddOns "'.$addonname.'" abgebrochen, da das übermittelte AddOn die Sicherheitsprüfungen nicht bestanden hat. Dies kann folgende Gründe haben: <br /><br />- Fehlerhafter AddOn-Key auf REDAXO.de eingetragen (Ordnername muss exakt wie AddonKey sein)<br />- Paket ist kein AddOn, sondern eine Modifikation o.ä. (Beschreibung beachten)<br />- Wichtige AddOn-Daten fehlen. (config.inc.php, install.inc.php etc.)<br /<br />Installer überträgt nur einwandfreie AddOns, um maximale Sicherheit zu bieten. Du kannst das Risiko jedoch selbst eingehen, und die <a href="'.$addonfile.'">Datei runterladen,</a> sichten und ggf. selbst hochladen.').'
				</div>';
		}	

	} else {
		echo '
			<div class="rex-addon-output">
				<h2 class="rex-hl2">Fehler</h2>
				'.rex_warning('Das AddOn "'.$addonname.'" konnte nicht von Redaxo.org übertragen werden!').'
			</div>';
	}
	
}
?>
