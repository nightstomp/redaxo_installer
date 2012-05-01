<?php
	// Verschiedene kleine Funktionen, um den Quellcode sauberer zu halten. Generiert Links
	
	// Link zur Redaxo.de Detailseite des Addons
	function Installer_getDetailUrl($addonDetailUrl, $addonId, $label, $addon_key)
	{
		return '<a name="'.$addon_key.'" href="'.$addonDetailUrl.$addonId.'" onclick="window.open(this.href); return false;">'.$label.'</a>';
	}
	
	// Gibt Link für den Installprozess aus
	// Macht den Quellcode schöner
	function Installer_getAddonInstallUrl($id, $installname, $path)
	{
		return '<a onclick="return loadViaAjax(this.rel);" rel="?page=installer&subpage=addon_installer&pluginpage=install&addonurl='.$path.'&addonid='.$id.'&installname='.$installname.'" href="#top"><img src="media/addons/installer/install.gif" alt="Mit Installer laden" title="Mit Installer laden" /></a>';
	}
	
	function Installer_getModuleInstallUrl($id)
	{
		return '<a onclick="return loadViaAjax(this.rel);" rel="?page=installer&subpage=modul_installer&pluginpage=install&moduleid='.$id.'" href="#top"><img src="media/addons/installer/install.gif" alt="Mit Installer laden" title="Mit Installer laden" /></a>';
	}
?>