$(window).load(function() {
	
	var mainBreaker = {
		'engage'	: true,
		'debug'		: true,
		'resize'	: true,
		'hresize'	: true,
		'vresize'	: true,
		'output'	: true,
	}
	
	if ( mainBreaker['engage'] ) {
		alignImage();
		sidebarHandles();
		positionAbsoluteCenter();
		zoomToTopLeft();
	}
	
	function alignImage(){
			if ( $('#frame').height() > $( "#image-plate" ).height() ) {
				var split = Math.floor( ( $('#frame').height() - $( "#image-plate" ).height() ) / 2 );
				$( '#image-plate' ).css( { 'top': split + 'px' } );
			}		
	}
	
	function zoomToTopLeft(){
		if ( $("#frame").width() <= 1366 ) {
			$( "#frame" ).one( "click", function(){
				if ( parseInt( $( "#image-plate" ).attr( "width" ) ) > $("#frame").width() ) {
					$( "#frame" ).fadeTo( 330, 0.1, function(){
						var height = parseInt( $( "#image-plate" ).attr( "height" ) );
						var top = Math.floor( ( $("#frame").height() - height ) / 2 ); 
						if ( top < 0 || height > 900 ) {
							top = 0;
						}
						$( "#image-plate" ).css( { 'top' : top + 'px', 'height': 'initial', 'width': 'initial' } );
					}).fadeTo( 160, 1 );
				}
			});
		}
	}
	
	function sidebarHandles(){
		$( "#handle" ).click( function(){ 
			$( ".tap-grid .hide:visible" ).hide();
			$( "#sidebar" ).fadeToggle( 330, "linear" ); 
		});
		$( "#handle-alt" ).click( function(){
			$( ".tap-grid .hide:visible" ).hide();
			$( "#sidebar-alt" ).fadeToggle( 330, "linear" ); 
		});
	}
	
	function positionAbsoluteCenter(){
		$("#phone-click").click( function(){
			$( ".tap-grid .hide:visible" ).not( "#phone" ).hide();
			$( "#phone" ).fadeToggle( 330, "linear" );
	    });
		
		$("#email-click").click( function(){
			$( ".tap-grid .hide:visible" ).not( "#email" ).hide();
			$( "#email" ).fadeToggle( 330, "linear" );
	    });
		
		$("#share-click").click( function(){
			$( ".tap-grid .hide:visible" ).not( "#share" ).hide();
			$( "#share" ).fadeToggle( 330, "linear" );
	    });		
	}
	
    function dlog( arr ) {
		if ( mainBreaker['debug'] ){
			console.log( arr );
		}
	}
	
});