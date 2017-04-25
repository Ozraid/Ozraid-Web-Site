jQuery( document ).ready( function( $ ) {
	
	var elements = {
		"cookies": "#cookies",
		"container": "#cookies-container",
		"content": "#cookies-content"
	};
	var classes = {
		"cookies": "action-cookies-accept",
		"hide": "hidden",
	};
	var interval = 1000;
	
	setTimeout( function() {
		$( elements.cookies ).fadeIn( interval, function() {
			$( elements.container ).slideDown( interval / 2, "linear" ).removeClass( classes.hide );
		}).removeClass( classes.hide );
	}, 2500 );
	
	$( "." + classes.cookies ).on( "click keyup", function( event ) {
		var key = event.keyCode || event.which;
		if ( key == 1 || key == 13 || key == "click" ) {
			$( elements.container ).slideUp( interval / 2, "linear", function() {
				$( elements.cookies ).fadeOut( interval ).addClass( classes.hide );
			}).addClass( classes.hide );
		}
	});
	
});