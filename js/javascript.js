jQuery(document).ready(function($) {
	
	//create our "breaker" switches
	var mainBreaker = {
		'engage'	: true,
		'overlay'	: true,			//overlay header and footer if allowed
		'resize'	: true,			//resize image
		'hresize'	: true,			//resize image width (height set to 100%)
    	'vresize'	: true,			//resize image height
    	'output'	: true,
	}
	
	var frameBreaker = {
    	'resize'	: false,		//resize frame
	}
	
	var scrollBreaker = {
    	'scroll'	: true,			//turn scroll off if not needed
    	'default'	: true,			//scroll behaviour is default
    	'hscroll'	: true,			//add horizontal scroll to image
    	'vscroll'	: true,			//add vertical scroll to image
    	'output'	: true,
	}
	
	var overflowBreaker = {
		'overflow'		: true,
    	'horizontal'	: true,		//hide horizontal overflow 
    	'vertical'		: true,		//hide vertical overflow
    	'output'		: true,
	}
	
	var alignBreaker = {
		'align'		: true,			//use alignment
		'halign'	: false,
		'valign'	: true,
		'center'	: false,			//center div or image 
    	'left'		: true,			//image or div to left
    	'output'	: true,
	}
	
	var debugBreaker = {
		'debug'		: true,			//debug
    	'output'	: true,
	}
	
	//values
	
	var currViewport = {
		'viewport'	: true,
		'width'		: $( window ).width(),
    	'height'	: $( window ).height(),
    	'output'	: true,
	}
	
	var currCorral = {
		'corral' 	: true,
    	'width'		: $( "#corral" ).width(),
    	'height'	: $( "#corral" ).height(),
    	'output'	: true,
	};
	
	var currFrame = {
		'frame' 	: true,
		'width'		: $( "#frame" ).width(),
		'height'	: $( "#frame" ).height(),
		'output'	: true,
    };
	
	var origImage = {
		'origImage'	: true,
		'width'		: parseInt( $( "#image-plate" ).attr( "width" ) ),
    	'height'	: parseInt( $( "#image-plate" ).attr( "height" ) ),
    	'output'	: true,
		}
		
	var currImage = {
		'currImage'	: true,
		'width'		: $( "#image-plate" ).width(),
		'height'	: $( "#image-plate" ).height(),
		'output'	: true,
	}
	
	var ratio = {
		'ratio'		: true,
    	'aspect'	: 1.7778,
    	'twidth'	: 1600,
    	'theight'	: 900,
    	'output'	: true,
	}
	
	var calculated = { 
		'aspect'	: null,
		'width'		: null,
		'height'	: null,
		'max-width'	: null,
		'max-height': null,
		'scroll'	: null,
		'margin'	: null,
		'output'	: true,		
	}
	
	var work = {
		'work'		: true,
		'frame'		: false,
		'resized'	: false,
		'width' 	: false,
		'height' 	: false,
		'max-width'	: false,
		'max-height': false,
		'resized'	: false,
		'hscroll' 	: false,
		'vscroll' 	: false,
		'align' 	: false,
		'halign' 	: false,
		'valign' 	: false,
		'output'	: true,
	}
		
	//get to work
	if ( mainBreaker['engage'] ) {
		if ( frameBreaker['resize'] ) { 
			resizeFrame();		
		}
		
		if ( mainBreaker['resize'] ) { 
			resizeImage();		
		}
		
		if ( alignBreaker['align'] ) { 
			alignImage();
		}
		rightHandle();
	}
	
	function resizeFrame() {
		if ( currFrame['width'] < ratio['twidth'] * .85 || currFrame['height'] < ratio['theight'] * .95 )  {
			$( "#frame" ).css( { 'top': 0 } );
			work['frame'] = true;
		} 
	}
	
	function resizeImage(){
		if ( origImage['width'] !== undefined ) {
			calculated['aspect'] = origImage['width'] / origImage['height'];
			calculated['height'] = origImage['height'];
			calculated['width'] = Math.round( origImage['height'] * calculated['aspect'] );
			}
		if ( currFrame['height'] < 900 * .95 ) {
			//$("#image-plate").css( { 'max-width' : '100%' } );
			//calculated['max-width'] = 'none';
			//work['max-width'] = true;
			$("#image-plate").css( { 'max-height' : '100%' } );
			calculated['max-height'] = 'none';
			work['max-height'] = true;
			if ( currFrame['width'] > ratio['twidth'] * .95 ) {
				work['resized'] = true;
				$("#image-plate").width( calculated['width'] );
			}			
		}
		
		if ( scrollBreaker['scroll'] ) { //turn the whole thing off if we don't need it.
			scrollImage();
		}
		
		setTimeout( logOutput(), 5000 );
		
	}
	
	function alignImage() {
		if ( alignBreaker['halign']){
			if ( currImage['width'] > currViewport['width'] ) {
				if ( $("#image-plate").hasClass( 'center' ) ) {
					var mleft = Math.round( ( currFrame['width'] - calculated['width'] ) / 2  );
			    	$('#image-plate').css( { 'margin-left' : mleft + 'px' } );
			    	calculated['margin'] = mleft;
		    	}
			}		
		}
		if ( alignBreaker['valign']){
			if ( $( "#frame" ).height() > origImage['height']  ) {
				var diff = $( "#frame" ).height() - $( "#image-plate" ).height();
				calculated['vdiff'] = diff;
				var top = diff / 2;
				$('#image-plate').css( { 'top' : top + 'px' } );
				calculated['valign'] = top;
		    	work['valign'] = true;
		    }		
		}
	}
	
	function scrollImage() {
		if ( origImage['width'] > currViewport['width'] ) {
			if ( scrollBreaker['scroll'] ) {
				work['hscroll'] = true;
				var sleft = ( currFrame['width'] - calculated['width'] ) / 2;
		    	$('#frame').scrollLeft( sleft );
		    	calculated['scroll-left'] = sleft;
		    	work['scroll-left'] = true;
		    	var h = $( "#image-plate" ).height();
		    	if ( $( "#frame" ).height() < h * .95 ) {
		    		$("#inner").css( { 'overflow-y': 'scroll' } );
		    		work['vscroll'] = true;
		    	}
		    	
		    	if ( currViewport['height'] < 950 ) {
			    	if ( currViewport['height'] > origImage['height'] *.88 ) {
			    		$("#inner").css( { 'overflow-y': 'initial' } );
			    		$("#image-plate").height( '100%' );
			    	}
		    	}
		    	if ( $( "#frame" ).width() < 1920 ) {
		    		if ( $( "#frame" ).width() > origImage['width'] * .79 ) {
		    			$("#inner").css( { 'overflow-x': 'initial' } );
			    		$("#image-plate").width( '100%' );
			    	}
	    		}
			}
		}
	}
	
	function logOutput(){
		if ( mainBreaker['output'] ){
				console.log( mainBreaker );
			if ( scrollBreaker['output'] )
				console.log( scrollBreaker );
			if ( overflowBreaker['output'] )
				console.log( overflowBreaker );
			if ( alignBreaker['output'] )
				console.log( alignBreaker );
			if ( debugBreaker['output'] )
				console.log( debugBreaker );
			if ( ratio['output'] )
				console.log( ratio );
			if ( currViewport['output'] )
				console.log( currViewport );
			if ( currCorral['output'] )
				console.log( currCorral );
			if ( currFrame['output'] )
				console.log( currFrame );
			if ( origImage['output'] )
				console.log( origImage );
			if ( currImage['output'] )
				console.log( currImage );
			if ( calculated['output'] )
				console.log( calculated );
			if ( work['output'] )
				console.log( work );
		}
	}
	
	function rightHandle(){
		$( "#right-handle" ).click( function(){ 
			$( "#right-sidebar" ).fadeToggle( 500, "linear" ); 
		});
	}
	
	function dlog( arr ) {
		if ( debugBreaker['debug'] ){
			console.log( arr );
		}
	}
});