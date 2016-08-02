<?php

list( $start, $sec ) = explode( " ", microtime() );

DEFINE( 'FINCH', true );
DEFINE( 'DEVELOPMENT', false );
DEFINE( 'PUBLISH', false );
DEFINE( 'CDN', false );
$cdn = 'https://cdn.uroburos.ca/cdn';

require_once( 'finch-engine.php' );

$page = get_finch_page();

$style_time = DEVELOPMENT ? '?' . time() : '';
$js_time = DEVELOPMENT ? '?' . time() : '';
$style_min = DEVELOPMENT ? '' : '.min';
$js_min = DEVELOPMENT ? '' : '.min';

header('Content-type: text/html; charset=utf-8;');
$str = '<!DOCTYPE html>' . PHP_EOL;
$str .= '<html lang=en>' . PHP_EOL;
$str .= '<head>' . PHP_EOL;
$str .= sprintf( '<title>%s</title>%s', $page['name'], PHP_EOL );
$str .= PUBLISH ? '<meta name="robots" content="noindex,nofollow">' . PHP_EOL : '';
$str .= '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">' . PHP_EOL;
$str .= '<meta name="viewport" content="width=device-width,minimum-scale=0.2,maximum-scale=1.8,initial-scale=1,user-scalable=yes">' . PHP_EOL;
if ( CDN ) {
	$str .= sprintf( '<link rel=stylesheet type="text/css" href="%s/css/style%s.css%s">%s', $cdn, $style_min, $style_time, PHP_EOL );
	$str .= $page['pdf'] ? get_finch_pdf_stylesheets() : ''; 
	$str .= sprintf( '<script type="text/javascript" src="%s/js/jquery-2.2.0.min.js" defer></script>%s', $cdn, PHP_EOL );
	$str .= sprintf( '<script type="text/javascript" src="%s/js/jquery%s.js%s" defer></script>%s', $cdn, $js_min, $js_time, PHP_EOL );
	$str .= $page['pdf'] ? get_finch_pdf_scripts( $page ) : '';
}
else {
	$str .= sprintf( '<link rel=stylesheet type="text/css" href="css/style%s.css%s">%s', $style_min, $style_time, PHP_EOL );
	$str .= $page['pdf'] ? get_finch_pdf_stylesheets() : '';
	$str .= '<script type="text/javascript" src="js/jquery-2.2.0.min.js" defer></script>' . PHP_EOL;
	$str .= sprintf( '<script type="text/javascript" src="js/jquery%s.js%s" defer></script>%s', $js_min, $js_time, PHP_EOL );
	$str .= $page['pdf'] ? get_finch_pdf_scripts( $page ) : '';
}
$str .= '</head>' . PHP_EOL;
$str .= sprintf( '<body%s>%s', get_finch_body_classes( $page ), PHP_EOL );
$str .= '<div id="corral">' . PHP_EOL;
$str .= $page['header'];
$str .= '<div id="frame">' . PHP_EOL;
$str .= $page['title'] . PHP_EOL;
$str .= '<div id="inner" class="inner hd-absolute">' . PHP_EOL;
if ( $page['pdf'] ) {
	$str .=  $page['pdf']; 
}
else {
	$str .= $page['html'] ? '<article id="article" class="columns two-columns">' . PHP_EOL : '';
	$str .= $page['video'] ? $page['video'] : $page['image']['tag'];
	$str .= $page['html'];
	$str .= $page['html'] ? '</article>' . PHP_EOL : '';
}
$str .= '</div>' . PHP_EOL; //inner
$str .= $page['audio'];
$str .= $page['sidebar'];
$str .= $page['tap-grid'];
$str .= '</div>' . PHP_EOL; //frame
$str .= $page['nav'];
$str .= $page['footer'];
list( $end, $sec ) = explode( " ", microtime() );
$str .= sprintf( '<div id="elapsed-time" class="tablet-hide">Elapsed: %sms</div>%s', number_format( ( (float)$end - (float)$start ) * 1000, 2, '.', ',' ) , PHP_EOL );
$str .= '</div>' . PHP_EOL; //corral
$str .= '</body>' . PHP_EOL;
$str .= '</html>' . PHP_EOL;
echo $str;

function get_finch_body_classes( $page ){
	$page['video'] ? $arr[] = 'video' : false;
	$page['audio'] ? $arr[] = 'audio' : false;
	if ( $page['layout']['sidebar'] ){
		$page['layout']['mobile-header-show'] ? $arr[] = 'mobile-header-show' : false;
		$page['layout']['tablet-header-show'] ? $arr[] = 'tablet-header-show' : false;
		$page['layout']['sidebar-overlay'] ? $arr[] = 'sidebar-overlay' : 'body-sidebar';
	}
	$classes = trim( implode( ' ', $arr ) );
	if ( ! empty ( $classes ) ){
		$str = sprintf( ' class="%s"', $classes );
		return $str;
	} else {
		return false;
	}
}
