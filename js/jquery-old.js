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
		if ( parseInt( $( "#image-plate" ).attr( "height" ) ) < $('#frame').height() )  {
			if( ! $('#image-plate').hasClass( 'relative' ) ) {
				if ( $('#frame').height() > $('#image-plate').height() ) {
					var split = Math.floor( diff / 2 );
					$( '#image-plate' ).css( { 'top': split + 'px' } );
				}
			}
		}		
	}
	
	function zoomToTopLeft(){
		if ( $("#frame").width() <= 1366 ) {
			$( "#frame" ).one( "click", function(){
				if ( parseInt( $( "#image-plate" ).attr( "width" ) ) > $("#frame").width() ) {
					$( "#frame" ).fadeTo( 330, 0.1, function(){
						var height = parseInt( $( "#image-plate" ).attr( "height" ) );
						var top = Math.floor( ( $("#frame").height() - height ) / 2 ); 
						if ( top < 0 || height >= 900 ) {
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
			$( "#sidebar" ).fadeToggle( 500, "linear" ); 
		});
		$( "#handle-alt" ).click( function(){
			$( ".tap-grid .hide:visible" ).hide();
			$( "#sidebar-alt" ).fadeToggle( 500, "linear" ); 
		});
	}
	
	function positionAbsoluteCenter(){
		$("#phone-click").click( function(){
			$( "#phone" ).fadeToggle( 500, "linear" );   	
	    });
		
		$("#email-click").click( function(){
			$( "#email" ).fadeToggle( 500, "linear" );
	    });
		
		$("#share-click").click( function(){
			$( "#share" ).fadeToggle( 500, "linear" );	    		
	    });		
	}
    function dlog( arr ) {
		if ( mainBreaker['debug'] ){
			console.log( arr );
		}
	}
	
});