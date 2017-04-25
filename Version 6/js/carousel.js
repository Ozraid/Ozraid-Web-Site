jQuery( document ).ready( function( $ ) {
	
	// @var object Carousel data. carousel = { "id": { "display", "elements", "width", "li": { "width", "first", "last" } } }
	var carousel = {};
	var display = {
		"fullscreen": {
			"elements": 4,
			"width": 1100,
			"padding": 10,
			"animation": "linear",
			"interval": 500
		},
		"screen": {
			"elements": 3,
			"width": 989,
			"padding": 10,
			"animation": "linear",
			"interval": 500
		},
		"tablet": {
			"elements": 2,
			"width": 739,
			"padding": 10,
			"animation": "linear",
			"interval": 500
		},
		"mobile": {
			"elements": 1,
			"width": 400,
			"padding": 10,
			"animation": "linear",
			"interval": 500
		}
	};
	var classes = {
		"carousel": "carousel",
		"container": "carousel-container",
		"hide": "carousel-hidden",
		"left_button": "carousel-button-left",
		"right_button": "carousel-button-right"
	};
	
	Init();
	
	$( window ).on( "resize", function () {
		SetDisplay();
	});
	
	$( "." + classes.right_button ).on( "click keypress", function() {
		var id = GetID( $( this ) ), type;
		carousel[id].li.first++;
		carousel[id].li.last++;
		type = carousel[id].display;
		carousel[id].position = ( carousel[id].li.width + display[type].padding ) * carousel[id].li.first;
		$( id ).children( "." + classes.left_button ).removeClass( classes.hide );
		$( "li", $( "#" + id ) ).each( function() {
			$( this ).animate({ "right": carousel[id].position }, display[type].interval, display[type].animation );
		});
		if ( carousel[id].position >= ( $( "#" + id ).find( "ul" ).width() - carousel[id].width - display[type].padding ) ) {
			SetButton( "right", "hide" );
		}
		else {
			SetButton( "right", "display" );
		}
		if ( carousel[id].position == 0 ) {
			SetButton( "left", "hide" );
		}
		else {
			SetButton( "left", "display" );
		}
	});
	
	$( "." + classes.left_button ).on( "click keypress", function() {
		var id = GetID( $( this ) ), type;
		carousel[id].li.first--;
		carousel[id].li.last--;
		type = carousel[id].display;
		carousel[id].position = ( carousel[id].li.width + display[type].padding ) * carousel[id].li.first;
		$( id ).children( "." + classes.left_button ).removeClass( classes.hide );
		$( "li", $( "#" + id ) ).each( function() {
			$( this ).animate({ "right": carousel[id].position }, display[type].interval, display[type].animation );
		});
		if ( carousel[id].position >= ( $( "#" + id ).find( "ul" ).width() - carousel[id].width ) ) {
			SetButton( "right", "hide" );
		}
		else {
			SetButton( "right", "display" );
		}
		if ( carousel[id].position == 0 ) {
			SetButton( "left", "hide" );
		}
		else {
			SetButton( "left", "display" );
		}
	});
	
	function Init() {
		SetCarouselVar();
		SetDisplay();
		SetButton( "left", "hide" );
	}
	
	function SetCarouselVar() {
		$( "." + classes.carousel ).each( function() {
			var id = $( this ).attr( "id" ), start;
			if ( $( "#" + id ).attr( "data-start" ) ) {
				start = parseInt( $( "#" + id ).attr( "data-start" ) ) - 1;
			}
			else {
				start = 0;
			}
			carousel[id] = {
				"elements": $( this ).find( "li" ).length,
				"li": {
					"first": start
				}
			};
		});
	}
	
	function SetDisplay() {
		var id, type;
		$( "." + classes.container ).each( function() {
			id = GetID( $( this ) );
			carousel[id].width = $( this ).width();
			for ( var type in display ) {
				if ( display.hasOwnProperty( type ) ) {
					if ( carousel[id].width >= display[type].width ) {
						break;
					}
					else {
						console.log( carousel[id].width );
						carousel[id].display = type;
						carousel[id].li.width = ( carousel[id].width - ( ( display[type].elements - 1 ) * display[type].padding ) ) / display[type].elements;
						carousel[id].position = ( carousel[id].li.width + display[type].padding ) * carousel[id].li.first;
						$( "li", $( this ) ).each( function() {
							$( this ).css({ "right": carousel[id].position, "width": carousel[id].li.width, "padding-right": display[type].padding });
						});
					}
				}
			}
		});
	}
	
	function SetButton( button, status ) {
		switch ( status ) {
			case "display":
				$( "." + classes[button + "_button"] ).removeClass( classes.hide );
				break;
			case "hide":
				$( "." + classes[button + "_button"] ).addClass( classes.hide );
				break;
		}
	}
	
	function GetID( element ) {
		return $( element ).parents( "." + classes.carousel ).attr( "id" );
	}
	
	
});