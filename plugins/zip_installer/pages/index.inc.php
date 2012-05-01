<?php
	$mypage		= "zip_installer";
	 
	// Parameter
	$Basedir = dirname(__FILE__);
	
	$page		= rex_request('page', 'string');
	$subpage	= rex_request('subpage', 'string');
	$pluginpage	= rex_request('pluginpage', 'string');
	$func		= rex_request('func', 'string');
	$ajax		= rex_request('viaAjax', 'string');


	if(!$ajax)
	{
		include $REX['INCLUDE_PATH'].'/layout/top.php';
		
		
		// Build Subnavigation 
		$subpages = array(array('','ZIP Installer'),);
		if(!empty($REX['ADDON']['installer_plugins'][$mypage]))
		{
			foreach($REX['ADDON']['installer_plugins'][$mypage] as $plugin => $pluginsettings)
			{
				if(!empty($pluginsettings['subpages']))
				{
					$subpages = array_merge($subpages, $pluginsettings['subpages']);
				}
			}
		}
		
		rex_title("Installer", $REX['ADDON'][$page]['SUBPAGES']);
	}

		
		// Include Current Page
		if(!$pluginpage)
		{
			switch($subpage)
			{
			    default:
			        require $Basedir .'/zip_upload.inc.php';
			}
		}
		
		if($pluginpage)
		{
			switch($pluginpage)
			{
			    case 'install':
			        require $Basedir .'/installer.inc.php';
			    break;
			    
			    default:
				        require $Basedir .'/zip_upload.inc.php';
			}
		}

		
		
		if(!$ajax)
		{
			// Include Footer 
			include $REX['INCLUDE_PATH'].'/layout/bottom.php';
		}		

?>