<?php
	
	// CALLBACK-Funktion, um CSS & JS im Backend einzubinden
	function Installer_add_assets($params)
	{
		$mypage = 'installer';	
		
		$params['subject'] .= "\n  ".
		'<link rel="stylesheet" type="text/css" href="media/addons/'.$mypage.'/screen.css" />';
		$params['subject'] .= "\n  ".
		'<script type="text/javascript" src="media/addons/'.$mypage.'/jquery.quicksearch.js"></script>'.
		'<script type="text/javascript" src="media/addons/'.$mypage.'/scripts.js"></script>';
	
		return $params['subject'];
	}
	
	// Löscht die SESSION mit den Addon-Werten. Zwingt Installer dazu, die Addon-List zu refreshen.
	function Installer_clearSessionCache()
    {
        unset($_SESSION['addonsAsArray']);
        unset($_SESSION['addonsAsObject']);
        unset($_SESSION['modulesAsArray']);
        unset($_SESSION['modulesAsObject']);
		unset($_SESSION['fastcheck']);
    }


	// Wandelt ein mittels json_decode erstelltes Object in ein Array
	// Funktion wird genutzt, um ein Multidimensionales json-Object in ein 
	// PHP-Array zu verwandeln, damit Funktionen wie array_splice und array_search
	// einfacher anzuwenden sind
	function Installer_object2array($object)
	{
	   $return = NULL;
	 
	   if(is_array($object))
	   {
	       foreach($object as $key => $value)
	           $return[$key] = Installer_object2array($value);
	   }
	   else
	   {
	       $var = @get_object_vars($object);
	 
	       if($var)
	       {
	           foreach($var as $key => $value)
	               $return[$key] = Installer_object2array($value);
	       }
	       else
	           return strval($object); // strval and everything is fine
	   }
	 
	   return $return;
	}
	
	
	// Erweitert array_search, damit auch multidimensional durchsucht werden kann
	// array_search durchsucht nur eindimensionale arrays
	function Installer_array_search_key($needle_key, $array) 
	{
		foreach($array AS $key=>$value)
		{
			if($key == $needle_key) return $value;
			if(is_array($value))
			{
				if(($result = Installer_array_search_key($needle_key,$value)) !== false)
				return $result;
			}
		}
		return false;
	}
	
    function cmp($a, $b)
    {
        return version_compare($b["file_version"], $a["file_version"]);
    }
        
	// Gruppiert Elemente mit der selben Addon-ID, damit Werte nicht 
	// zwei mal in der Hautpliste auftreten und in der Detail-Install-Liste als
	// unterschiedliche Versionen angezeigt werden
	function Installer_group_addons_by_id($addons)
	{
		$result = array();
		
		if(is_array($addons)) {
			foreach ($addons as $el) 
			{
				if (!array_key_exists($el['addon_id'], $result)) {
					$result[$el['addon_id']] = array();
				}
			
				$result[$el['addon_id']][] = array_slice($el, 1);
			}
			return $result;
		} else {
			echo $result;
		}
	} 
	
	function Installer_timestamp_convert($date) {
		$stamp['date']    =    sprintf("%02d.%02d.%04d",
								 substr($date, 6, 2),
								 substr($date, 4, 2),
								 substr($date, 0, 4));
												
		$stamp['time']    =    sprintf("%02d:%02d:%02d",
								 substr($date, 8, 2),
								 substr($date, 10, 2),
								 substr($date, 12, 2));

		return $stamp;
	}
?>