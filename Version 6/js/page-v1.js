jQuery( document ).ready( function( $ ) {
	
	var uri, target;
	var elements = {
		"link": "a",
		"page": "html",
		"background": "body",
		"container": "#site-container",
		"footer": "#site-footer"
	}
	var classes = {
		"hide": "hidden",
		"footer": "site-footer-page"
	};
	var interval = 2500;
	
	if ( window.location.hash ) {
		setTimeout( function() {
			location.hash = window.location.hash;
		}, 0 );
	}
	
	$( elements.page ).fadeIn( interval ).removeClass( classes.hide );
	$( elements.background ).fadeIn( interval );
	
	if ( $( elements.footer ).hasClass( classes.footer ) ) {
		$( elements.container ).css( "padding-bottom", $( elements.footer ).height() + 20 );
	}
	
	$( elements.a ).on( "click", function( event ) {
		event.preventDefault();
		if ( $( this ).attr( "rel" ) == "nofollow" || $( this ).hasClass( "nofollow" ) ) {
			return;
		}
		else if ( $( this ).attr( "href" ) ) {
			uri = $( this ).attr( "href" );
		}
		else {
			return;
		}
		if ( $( this ).attr( "target" ) ) {
			target = $( this ).attr( "target" );
		}
		else {
			target = "_self";
		}
		if ( target == "_blank") {
			window.open( uri, target );
		}
		else if ( uri.search( /^#/i ) != -1 ) {
			setTimeout( function() {
				location.hash = uri;
			}, interval / 2 );
			$( elements.page ).fadeOut( interval / 2 ).addClass( classes.hide ).fadeIn( interval  / 2 ).removeClass( classes.hide );
			$( elements.background ).fadeOut( interval / 2 ).fadeIn( interval  / 2 );
		}
		else {
			$( elements.page ).fadeOut( interval, function() {
				location.href = uri;
			}).addClass( classes.hide ).fadeIn( interval  / 2 ).removeClass( classes.hide );
			$( elements.background ).fadeOut( interval ).fadeIn( interval  / 2 );
		}
	});
		
});