<a name="top" id="top"></a>
<div id="ajax-result"></div>
<?php
	ini_set('display_errors', 0);
    // Variablen
	$parent                 = 'installer';
	$mypage                 = 'modul_installer';
	$page                   = rex_request('page', 'string');
	$subpage                = rex_request('subpage', 'string');
	$func                   = rex_request('func', 'string');
	//$basedir	            = $REX['ADDON']['dir'][$mypage];
	$moduleApi				= 'http://www.redaxo.org/de/_system/_webservice/module/?v='.$REX['VERSION'].'.'.$REX['SUBVERSION'];
	$moduleDetailUrl		= 'http://www.redaxo.org/de/download/module/?modul_id=';
	$addon = array();
	$module = array();
	
	// Leere Vars, wg, PHP Warning
	$foundSomething         = ''; // leere var für die Suche
	$moduleList             = '';
	
	// Get Search ;)
	$search_module          = rex_request('search_module', 'string');
	
	// Prüfen, ob SESSION der Modules-Liste bereits vorliegt
	// In diesem Case wird das zurückgelieferte Ergebnis mittels Funktion
	// von einem Object in ein Array umgewandelt und zusätzlich eine Varriante
	// als object gespeichert

	Installer_clearSessionCache(); // Die Session muss jedes mal zerstört werden, da die Daten immer frisch abgerufen werden müssen   
	
	$getModules						= Installer_getAddons($moduleApi."&vt=".$search_module);
	$_SESSION['modulesAsArray']		= Installer_object2array($getModules);
	$_SESSION['modulesAsObject']	= $getModules;
			
	if(is_array($_SESSION['modulesAsArray'])){
    	foreach ($_SESSION['modulesAsArray'] as $key => $module)
    	{
    		$moduleList .= '<tr data-addon="?page=installer&subpage=modul_installer&pluginpage=install&moduleid='.$module['id'].'">';
    		$moduleList .= '<td>'.$module['name'].'</td>';
    		$moduleList .= '<td>'.$module['shortdescription'].'</td>';
    		//$moduleList .= '<td><textarea>'.htmlentities($module['module_in']).'</textarea></td>';
    		$moduleList .= '<td><img src="media/addons/installer/install.gif" alt="Mit Installer laden" title="Mit Installer laden" /></td>';
    		$moduleList .= '</tr>';
    		
    		$foundSomething = $module['id']; // Bissi dirty, benötige jedoch einen Wert um zu Prüfen, ob Suche korrekt war, da das Array immer gefüllt ist.
    	}
    }
	

?>


	<?php echo rex_warning('ACHTUNG: Der Modulinstaller liefert aktuell wenige Ergebnisse für neue Redaxo-Versionen, da die Entwickler die Versionsangaben nicht angepasst haben.'); ?>

	<div class="rex-addon-output">
		<h2 class="rex-hl2">Module suchen</h2>
		    <form method="post" action="">
		        <input type="hidden" name="page" value="installer" />
		        <input type="hidden" name="subpage" value="modul_installer" />
                <input type="text" id="search_module" name="search_module" value="<?php echo $search_module; ?>" /><input type="submit" value="" id="search_lupe" />
            </form>
	</div>
	
	<?php
	    if($search_module != "")
    	{
    	    if ($foundSomething)
        	{
	?>
            	<div class="rex-addon-output">
            		<h2 class="rex-hl2">Module Installieren (<?php echo count($_SESSION['modulesAsArray']); ?> gefunden)</h2>
            		
            				<table class="rex-table" id="installer-addonlist">
            					<tr>
            						<th>Modul-Name</th>
            						<th>Beschreibung</th>
            						<th>Aktion</th>
            					</tr>
            					<?php 
            						echo $moduleList; // Ausgabe der Ergebnisse
            					?>
            				</table>
            	</div>
	<?php
            } else {
                echo rex_warning('Die Suche nach '.$search_module.' lieferte leider keine Ergebnisse');
            }
        }
	?>