jQuery( document ).ready( function( $ ) {
	
	var elements = {
		"a": ".action-login",
		"login": "#header-login",
		"nav": "#primary-menu",
		"li": "li.action-login"
	};
	var classes = {
		"hide": "hidden",
		"active": "background-grey",
		"inactive": "background-hover-grey"
	};
	var position = {
		"start": "0px",
		"end": $( elements.nav ).height()
	};
	var interval = 500;
	
	$( window ).on( "resize", function () {
		position.end = $( elements.nav ).height();
		if ( $( elements.login ).hasClass( classes.hide ) ) {
		}
		else {
			$( elements.login ).css( "bottom", position.end );
		}
	});
	
	$( elements.a ).on( "click", function( event ) {
		if ( $( elements.login ).hasClass( classes.hide ) ) {
			$( elements.login ).removeClass( classes.hide );
			$( elements.li ).removeClass( classes.inactive ).addClass( classes.active );
			$( elements.login ).animate({ "bottom": position.start + "px", "bottom": position.end + "px" }, interval );
		}
		else {
			$( elements.login ).animate({ "bottom": position.end, "bottom": position.start }, interval, function() {
				$( this ).addClass( classes.hide );
				$( elements.li ).removeClass( classes.active ).addClass( classes.inactive );
			});
		}
	});
	
});