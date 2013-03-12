<?php
	// Alle GET-Parameter in $var speichern
	$moduleid		= rex_request('moduleid', 'int');
	$module_id		= rex_request('module_id', 'int');
	$module_name	= rex_request('module_name', 'string');
	$func			= rex_request('func', 'string');
	
	if($moduleid != "")
	{
		
		// Funktion aufrufen, um JSON von Redaxo-API zurückzubekommen
		// Rufe Session ab, welche bereits auf der addons.inc.php gespeichert wurde
		$modules = $_SESSION['modulesAsObject'];
?>
	<div class="rex-addon-output">
		<h2 class="rex-hl2">Verfügbare Downloads
		    <div class="action_bar_wrapper">
                <a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            </div>
		</h2>
			<?php 
				
				echo rex_info('Bitte wähle jetzt ein Modul.<br />Alle hier gezeigten Downloads sind laut Entwickler kompatibel mit deinem System REX '.$REX['VERSION'].'.'.$REX['SUBVERSION'].'.'.$REX['MINORVERSION'].'');

			?>
		<div class="rex-addon-content">
			<p class="rex-tx1">

				<?php
					// Zähle Ergebnisse in Array
					$ergebnis = sizeof($modules);
					
					if($ergebnis > 0)
					{
						echo '
						<table class="rex-table">
							<tr>
								<th>Modul</th>
								<th>Beschreibung</th>
								<th>Moduleingabe</th>
								<th>Modulausgabe</th>
								<th>Uploaddatum</th>
								<th>Aktion</th>
							</tr>';
						foreach($modules as $module)
						{
							// Wir Filtern nach modul id
							if($module->id == $moduleid)
							{
							    // Damit wir bei einer Installation an die In- und Outputs kommen, speichern wir diesen in sessions
							    // um später nicht erneut alle Daten zu parsen
								$_SESSION['module_in'][$module->id]     = $module->input;
								$_SESSION['module_out'][$module->id]    = $module->output;
								$dateTime = Installer_timestamp_convert($module->create_date);
								
								echo '
									<tr>
										<td>' . $module->name . '</td>
										<td>' . $module->description . '</td>
										<td><textarea onfocus="this.select();">' . htmlentities(utf8_decode($module->input)) . '</textarea></td>
										<td><textarea onfocus="this.select();">' . htmlentities(utf8_decode($module->output)) . '</textarea></td>
										<td>' . $dateTime['date'] . ' um ' . $dateTime['time'] .'</td>
										<td><a href="#" class="install-addon-link" data-addon="?page=installer&subpage=modul_installer&pluginpage=install&func=curl&module_id='.$module->id.'&module_name='.urlencode($module->name).'"><img src="media/file_add.gif" title="Installieren" alt="Installieren"/></a></td>
									</tr>';
							}
							
						}
					} else {
						echo rex_warning('Leider hat der Ersteller dieses Moduls keine Input und Output Daten bereitgestellt oder diese in der Zwischenzeit gelöscht');
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
    //  New Sql-Factory, um die Daten in die Datenbank zu schreiben
	$mi = new rex_sql;
	$mi->setTable("rex_module");
	$mi->setValue("eingabe",addslashes($_SESSION['module_in'][$module_id]));
	$mi->setValue("ausgabe",addslashes($_SESSION['module_out'][$module_id]));
	$mi->setValue("name",$module_name);
	
	unset($_SESSION['module_in'][$module_id]);
	unset($_SESSION['module_out'][$module_id]);
		
	if($mi->insert())
	{
		echo '
			<div class="rex-addon-output">
				<h2 class="rex-hl2">Installation abgeschlossen</h2>
				<div class="action_bar_wrapper">
                	<a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            	</div>
				'.rex_info('Das Modul "'.$module_name.'" wurde installiert und steht nun unter  <a href="?page=module">Module</a> zur Verfügung.').'
			</div>';


	} else {
		echo '
			<div class="rex-addon-output">
				<h2 class="rex-hl2">Fehler</h2>
				<div class="action_bar_wrapper">
                	<a href="#" class="closebox"><img src="media/addons/installer/close.gif" /></a>
            	</div>
				'.rex_warning('Das Modul konnte nicht installiert werden!').'
			</div>';
	}
	
}
?>