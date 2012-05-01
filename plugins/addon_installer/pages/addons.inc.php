<?php
    // Variablen
    $force = rex_request('force','string');
	$addonList	= '';
	$updatesAvailable = 0;
	$installer_version = '';
	$addon_text_overview = "";
	$addon_text_plugins = "";
?>

<a name="top" id="top"></a>
<div id="ajax-result">
    <?php
    	if($force == "refreshList") {
        Installer_clearSessionCache();
        echo rex_info('Die Liste wurde aktualisiert');
	}
	?>
</div>
<?php
	
	// Prüfen, ob SESSION der Addon-Liste bereits vorliegt
	// In diesem Case wird das zurückgelieferte Ergebnis mittels Funktion
	// von einem Object in ein Array umgewandelt und zusätzlich eine Varriante
	// für die installer.inc.php als object gespeichert
	
	
	if(!isset($_SESSION['addonsAsArray']))
	{
		$getAddons					= Installer_getAddons($addonApi);
		$_SESSION['addonsAsArray']	= Installer_object2array($getAddons);
		$_SESSION['addonsAsObject']	= $getAddons;
		
	} else {
		// Keine erneute Abfrage an Redaxo.de stellen
		// Array bereits in Session gespeichert
	}
	
	//print_r($result);
	
	//print_r($_SESSION['addonsAsObject']);
	
	if(is_array($_SESSION['addonsAsArray'])) {
		foreach (Installer_group_addons_by_id($_SESSION['addonsAsArray']) as $key => $addon)
		{
			// Damit nur die neueste Version in der Liste erscheint, müssen wir hier
			// noch mal eine Sortierungsfunktion aufrufen
			usort($addon, "cmp");
			
			$addon_status = '';
			if(OOAddon::isAvailable($addon[0]['addon_key']))
			{
				$addon_status = '<img src="media/addons/installer/play.gif" title="Installert und aktiviert" alt="Installiert und aktiviert" />';
				
			} 
			
			elseif(OOAddon::isInstalled($addon[0]['addon_key']) && !OOAddon::isActivated($addon[0]['addon_key'])) {
				$addon_status = '<img src="media/addons/installer/pause.gif" title="Installiert, jedoch nicht aktiviert" alt="Nicht aktiviert" />';
			}
			
			elseif(Installer_check_if_addon_exists($REX['INCLUDE_PATH']."/addons/".$addon[0]['addon_key']))
			{
				$addon_status = '<img src="media/addons/installer/on_server.gif" title="Im AddOn Ordner vorhanden" alt="Im AddOn Ordner vorhanden" />';
				
			} else {
				
				$addon_status = '<img src="media/addons/installer/addon.gif" title="Nicht auf Server vorhanden" alt="Nicht auf dem Server" />';
			}
			

			
			
			
			
			if(OOAddon::isAvailable($addon[0]['addon_key']) && !OOAddon::getVersion($addon[0]['addon_key']))
			{
				$systemVersion = '<img src="media/addons/installer/no_version.gif" title="Versionsangabe in der config.inc.php fehlt" alt="Versionsangabe in der config.inc.php fehlt" />';
				
			} 	
			
			elseif(!OOAddon::isAvailable($addon[0]['addon_key'])) 
			{
				$systemVersion = '-';	
			} else {
				$systemVersion = OOAddon::getVersion($addon[0]['addon_key']);
			} 

			$updateAvailable = "";
			if(checkAddonVersion(OOAddon::getVersion($addon[0]['addon_key']), $addon[0]['file_version']))
			{
				$updateAvailable = 'class="updateAvailable"';
				$updatesAvailable++; // Wert für Verfügbare Addons hochzählen
			}
			
			if($addon[0]['addon_key'] == "installer"){
				$installer_version = $addon[0]['file_version'];
			}
		
			
			$addonList .= '<tr '.$updateAvailable.' class="searchable">';
			$addonList .= '<td>'.Installer_getDetailUrl($addonDetailUrl, $key, $addon[0]['addon_name'], $addon[0]['addon_key']).'</td>';
			$addonList .= '<td>'.$addon[0]['file_version'].'</td>';
			$addonList .= '<td>'.$systemVersion.'</td>';
			
			$addon_text_overview = $addon[0]['addon_shortdescription'];
			
			if($REX["ADDON"]["installer"]["settings"]["SELECT"]["linkconvert_overview"]) {
				$addon_text_overview = Installer_url_to_link($addon_text_overview);
			}
			
			if($REX["ADDON"]["installer"]["settings"]["SELECT"]["nl2br_overview"]) {
				$addon_text_overview = nl2br($addon_text_overview);
			}
			
			$addonList .= '<td>'.$addon_text_overview.'</td>';
			$addonList .= '<td class="td_status">'.$addon_status.'</td>';
			$addonList .= '<td "class="td_dl">'.Installer_getAddonInstallUrl($key, $addon[0]['addon_key'], $addon[0]['file_path']).'</td>';
			$addonList .= '</tr>';
			
		}
		
		if(checkAddonVersion(OOAddon::getVersion('installer'), $installer_version)){
			echo rex_info('Es steht ein Update des Installers zur Verfügbar. <a href="#installer">Jetzt Installieren</a>');
		}
?>

    <div class="addon-header-wrapper">
        <?php
		if($REX["ADDON"]["installer"]["settings"]["SELECT"]["display_legend"])
		{
		?>
        <div class="rex-addon-output float_it">
    		<h2 class="rex-hl2">Legende</h2>
            	<table class="rex-table">
        			<tr>
        				<th>Symbol</th>
        				<th>Erklärung</th>
        			</tr>
        			
        			<tr>
        				<td><img src="media/addons/installer/addon.gif" alt="" /></td>
        				<td>AddOn ist noch nicht auf dem Server</td>
        			</tr>
        			
        			<tr>
        				<td><img src="media/addons/installer/on_server.gif" alt="" /></td>
        				<td>AddOn ist auf dem Server (im AddOn-Verzeichnis, jedoch nicht installert oder aktiviert)</td>
        			</tr>
        			
        			<tr>
        				<td><img src="media/addons/installer/play.gif" alt="" /></td>
        				<td>AddOn bereits installiert und aktiviert</td>
        			</tr>
        			
        			<tr>
        				<td><img src="media/addons/installer/pause.gif" alt="" /></td>
        				<td>AddOn installiert, jedoch nicht aktiviert</td>
        			</tr>
        			
        			<tr>
        				<td><img src="media/addons/installer/no_version.gif" alt="" /></td>
        				<td>Versionsangabe in der config.inc.php fehlt</td>
        			</tr>
        			
        		</table>
    	</div>
		<?php
		}
		if($REX["ADDON"]["installer"]["settings"]["SELECT"]["display_information"])
		{
		?>

    	<div class="rex-addon-output float_it last">
    
    		<h2 class="rex-hl2">Informationen 
    		    <div class="action_bar_wrapper"><a title="Ruft die Liste erneut von Redaxo.org ab und leert den Cache der Liste" href="?page=installer&subpage=<?php echo $mypage ?>&force=refreshList"><img src="media/addons/installer/refresh.gif" alt="Refresh" /></a></div>
    		</h2>
            	<table class="rex-table">
        			<tr>
        				<th>Info</th>
        				<th>Wert</th>
        				<th>Info</th>
        			</tr>
        			
        			<tr>
        			    <td>Installer Version</td>
        			    <td><?php echo OOAddon::getVersion('installer'); ?></td>
        			    <td><?php
                            if(checkAddonVersion(OOAddon::getVersion('installer'), $installer_version)){
                                echo 'Update Verfügbar. <a href="#installer">Jetzt Installieren</a>';
                            } else {
                                echo "Neueste Version installert";
                            }
                            ?>
        			    </td>
        			</tr>
        			
        			<tr>
        			    <td>Verfügbare AddOns für Deine Version (REX <?php echo $REX['VERSION'].'.'.$REX['SUBVERSION']; ?>)</td>
        			    <td><?php echo count(Installer_group_addons_by_id($_SESSION['addonsAsArray'])); ?></td>
        			    <td></td>
        			</tr>
        			    			
        			<tr>
        			    <td>Verfügbare Updates</td>
        			    <td><?php echo $updatesAvailable; ?></td>
        			    <td></td>
        			</tr>
        		</table>
    	</div>
		<?php
		}
		?>
    </div>
	
	<div class="clear_it"></div>
	
	<div class="rex-addon-output">
		<h2 class="rex-hl2">AddOns durchsuchen</h2>
            <input type="text" id="search_addon" name="search_addon" />
	</div>
	
	<div class="rex-addon-output">
		<h2 class="rex-hl2">AddOns installieren
		    <div class="action_bar_wrapper"><a title="Ruft die Liste erneut von Redaxo.org ab und leert den Cache der Liste" href="?page=installer&subpage=<?php echo $mypage ?>&force=refreshList"><img src="media/addons/installer/refresh.gif" alt="Refresh" /></a>
		    </div>
	    </h2>
				<table class="rex-table" id="addonList">
					<tbody>
						<tr>
							<th>Addon-Name</th>
							<th>Verfügbare Version</th>
							<th>Installierte Version</th>
							<th>Beschreibung</th>
							<th><img src="media/metainfo.gif" alt="info" /></th>
							<th>Aktion</th>
						</tr>
						<?php 
							echo $addonList; // Ausgabe der Ergebnisse
						?>
						
						<tr class="noHover" id="noresults">
							<td colspan="6">Sorry, die Suche lieferte leider keine Resultate!</td>
						</tr>
					</tbody>
				</table>
	</div>
	
<?php
} else {
	echo rex_warning('Leider kann dieses AddOn auf dem Server nicht ausgeführt werden, da URL-ACCESS mittels file_get_contents auf diesem Server deaktiviert scheint. Bitte kontaktiere deinen Serveradministrator und bitte um eine Freischaltung.<br /><br />Demnächst wird das AddOn so erweitert, damit es mittels Sockets arbeitet, dadurch ist es fast ohne Einschränkungen überall nutzbar. Ich informiere euch über das Redaxo-Forum, wenn das Update fertiggestellt ist');
}
?>