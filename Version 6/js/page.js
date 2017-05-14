jQuery( document ).ready( function( $ ) {
	
	var uri, target;
	var elements = {
		"a": "a",
		"page": "html",
		"container": "#site-container",
		"footer": "#site-footer",
		"checkbox": "input[type='checkbox'] + label",
		"radio": "input[type='radio'] + label"
	}
	var classes = {
		"hide": "hidden",
		"footer": "site-footer-page"
	};
	var interval = 2500;
	
	// Redirects bookmark URI or <a> links to the element with the 'window.location.hash' HTML5 id attribute.
	if ( window.location.hash ) {
		setTimeout( function() {
			location.hash = window.location.hash;
		}, 0 );
	}
	
	/**
	 * If <body> <footer> possesses the class 'classes.footer', resizes page container <div> CCS3 'padding-bottom' declaration
	 * to ensure <body> <footer> is placed at the bottom of the page.
	 */
	if ( $( elements.footer ).hasClass( classes.footer ) ) {
		setTimeout( function() {
			$( elements.container ).css( "padding-bottom", $( elements.footer ).height() );
		}, 1 );
	}
	
	// Fades in 'elements.page' element, and removes CCS3 'display: none;' declaration.
	$( elements.page ).fadeIn( interval ).removeClass( elements.hide );
	
	/**
	 * Fade out and in on clicking an <a> link element that:
	 *  - Possesses the <a href="$uri"> attribute
	 *  - Do not possess the <a rel="nofollow"> or <a class="nofollow"> attributes.
	 */
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
		}
		else {
			$( elements.page ).fadeOut( interval, function() {
				location.href = uri;
			}).addClass( classes.hide ).fadeIn( interval ).removeClass( classes.hide );
		}
	});
	
	/**
	 * Enables CSS3 custom checkbox. When <input type="checkbox" name="$name" id="$id" /> <label for="$id"> is clicked:
	 *  - Unckecks checkbox if checked
	 *  - Checks checkbox if unchecked.
	 */
	$( elements.checkbox ).on( "click", function() {
		var id = "#" + $( this ).attr( "for" ), checked = $( id ).prop( "checked" );
		if ( checked === true ) {
			$( id ).attr( "checked", false );
		}
		else if ( checked === false ) {
			$( id ).attr( "checked", false );
		}
	});
	
	 // Enables CSS3 custom radio buttons. When <input type="radio" name="$name" id="$id"/> <label for="$id"> is clicked, checks radio button.
	$( elements.radio ).on( "click", function() {
		var id = "#" + $( this ).attr( "for" ), checked = $( id ).prop( "checked" );
		if ( checked === false ) {
			$( id ).attr( "checked", true );
		}
	});
		
});