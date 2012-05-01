<?php

// Funktion, welche überprüft, ob es sich hierbei um ein Addon handelt
// Es wird geprüft, ob Dateien wie confic.inc.php, istall.inc.php existieren
// und ob der erste Rückgabewert aus dem ZIP ein Verzeichnis ist.
function zip_installer_check_if_file_is_addon($zipList)
{
    if(!is_array($zipList)){
        return false;
    }
    
	$returnValue = '';
	$i = 1; // ja, $i ist 1 :)
	foreach($zipList as $el)
	{
		// Wir brauchen nur das erste Element
		if($i == 1)
		{
			$installname = trim($el['file_name']);
			$returnValue = true;
		} else {
			
			// Alles danach ist unwichtig - break!
			break;
		}	
		
		$i++;
	} // end foreach
	
	if($returnValue) {
		// Prüft, ob wichtige Dateien, welche von einem Addon benötigt werden, exisitieren
		// Wenn nicht, handelt es sich um ein AddOn, sondern eventuell um einen Path,
		// oder eine Modifikation. Diese Daten können nicht vom Installer genutzt werden
		if(Installer_array_search_key($installname.'config.inc.php',$zipList) == false OR 
			Installer_array_search_key($installname.'install.inc.php',$zipList) == false OR
			Installer_array_search_key($installname.'uninstall.inc.php',$zipList) == false)
		{
			// Fehler, false zurück
			$returnValue = false;

		} else {
			$returnValue = $installname;
			// Jo, alles gut :)
		}
		
		// Wenn die Funktion nicht vorher schon durch ein return false abgebrochen wurde,
		// waren die Checks erfolgreich :) - Dann darf das AddOn auch installiert werden
		return $returnValue;
	}
}
?>