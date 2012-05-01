	jQuery(function(){
	    jQuery('a[href*=#]').click(function() {
		    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
		        && location.hostname == this.hostname) {
		            var $target = jQuery(this.hash);
		            $target = $target.length && $target || jQuery('[name=' + this.hash.slice(1) +']');
		            if ($target.length) {
		                var targetOffset = $target.offset().top;
		                jQuery('html,body').animate({scrollTop: targetOffset}, 1000);
		                return false;
		            }
		        }
	    });
	});

	
	function loadViaAjax(url)
	{
		jQuery('#ajax-result').html('<img src="media/addons/installer/spinner.gif" alt="Loading" /> Lade Daten...');
		
		jQuery('#ajax-result').load(url + '&viaAjax=1', function() {			
		});
		return false;
	}
	
	function closeBox(el){
		
	    jQuery(el).parents('div.rex-addon-output').animate({'height': '0px'});
		

	}
	
	function clearBox() {
	    jQuery('#ajax-result').empty();
	}
	
	jQuery(document).ready(function () {
		
		var searchSel = jQuery("#search_addon");
		searchSel.val('');
		searchSel.focus();
		
		searchSel.quicksearch("table#addonList tbody tr.searchable", {
			noResults: '#noresults',
			stripeRows: ['odd', 'even'],
			loader: 'span.loading'
		});
	});