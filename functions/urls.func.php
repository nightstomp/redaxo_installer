<?php
	// Verschiedene kleine Funktionen, um den Quellcode sauberer zu halten. Generiert Links
	
	// Link zur Redaxo.de Detailseite des Addons
	function Installer_getDetailUrl($addonDetailUrl, $addonId, $label, $addon_key)
	{
		return '<a name="'.$addon_key.'" href="'.$addonDetailUrl.$addonId.'" onclick="window.open(this.href); return false;">'.$label.'</a>';
	}	
?>