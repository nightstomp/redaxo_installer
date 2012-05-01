<?php
	function checkAddonVersion($intern, $extern)
	{
		if(!$intern)
		{
			return false;
		}
		return version_compare($intern, $extern, "<");
	}