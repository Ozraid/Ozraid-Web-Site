jQuery( document ).ready( function( $ ) {
	
	// @var object Carousel data. carousel = { "id": { "width", "elements", "display", "li": { "width", "position" } } }
	var carousel = {};
	var display = {
		"fullscreen": {
			"elements": 4,
			"width": 1100,
			"animation": "linear",
			"interval": 500
		},
		"screen": {
			"elements": 3,
			"width": 989,
			"animation": "linear",
			"interval": 500
		},
		"tablet": {
			"elements": 2,
			"width": 739,
			"animation": "linear",
			"interval": 500
		},
		"mobile": {
			"elements": 1,
			"width": 400,
			"animation": "linear",
			"interval": 500
		}
	};
	var classes = {
		"carousel": ".carousel",
		"left_button": ".carousel-button-left",
		"right_button": ".carousel-button-right",
		"hide": "carousel-hidden"
	};
	
	$( classes.carousel ).each( function() {
		var id = "#" + $( this ).attr( "id" ), type;
		$( classes.left_button ).addClass( classes.hide );
		SetCarousel( id );
		SetListElements( id );
		type = carousel[id].display;
		if ( carousel[id].elements <= display[type].elements ) {
			$( classes.right_button ).addClass( classes.hide );
		}
	});
	
	$( window ).on( "resize", function () {
		$( classes.carousel ).each( function() {
			var id = "#" + $( this ).attr( "id" );
			var li = {
				"width": carousel[id].li.width,
				"position": carousel[id].li.position
			};
			li.element = parseInt( li.position / li.width );
			console.log( li.element );
			SetListElements( id );
			carousel[id].li.position = carousel[id].li.width * li.element;
			$( "li", $( id ) ).each( function () {
				$( this ).css({ "right": carousel[id].li.position });
			});
		});
	});
	
	$( classes.carousel + " button" ).on( "click keyup", function( event ) {
		var key = event.keyCode || event.which, id = GetID( $( this ) ), button = "." + $( this ).attr( "class" ), type = carousel[id].display,
			end_position = carousel[id].li.width * ( carousel[id].elements - display[type].elements );
		if ( key == 1 || key == 13 || key == "click" ) {
			switch ( button ) {
				case classes.left_button :
					carousel[id].li.position -= carousel[id].li.width;
					if ( carousel[id].li.position < 10 ) {
						carousel[id].li.position = 0;
					}
					break;
				case classes.right_button :
					carousel[id].li.position += carousel[id].li.width;
					if ( carousel[id].li.position > carousel[id].li.width * ( carousel[id].elements - display[type].elements ) ) {
						 carousel[id].li.position = carousel[id].li.width * ( carousel[id].elements - display[type].elements );
					}
					break;
			}
			SetButton( id, "left_button", 0 );
			SetButton( id, "right_button", end_position );
			$( "li", $( id ) ).each( function () {
				$( this ).animate({ "right": carousel[id].li.position }, display[type].interval, display[type].animation );
			});
		}
	});
	
	function SetCarousel( id ) {
		carousel[id] = {
			"elements": $( id ).find( "li" ).length,
			"li": {
				"width": 0,
				"position": 0
			}
		};
	}
	
	function SetListElements( id ) {
		carousel[id].width = $( id ).width();
		for ( var type in display ) {
			if ( display.hasOwnProperty( type ) && carousel[id].width <= display[type].width ) {
				carousel[id].display = type;
				carousel[id].li.width = ( carousel[id].width - 10 ) / display[type].elements;
			}
		}
		$( "li", $( id ) ).each( function () {
			$( this ).css({ "min-width": carousel[id].li.width });
		});
	}
	
	function SetButton( id, button, comparison ) {
		if ( carousel[id].li.position == comparison ) {
			$( classes[button] ).addClass( classes.hide );
		}
		else if ( $( classes[button] ).hasClass( classes.hide ) ) {
			$( classes[button] ).removeClass( classes.hide );
		}
	}
	
	function GetID( element ) {
		return "#" + $( element ).parents( classes.carousel ).attr( "id" );
	}
	
});