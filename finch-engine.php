<?php

defined( 'FINCH' ) || die();

function get_finch_page () {
	$clean_url = false;
	$page_type = false;
	$page = false;
	$url = substr( $_SERVER['REQUEST_URI'], 1, 65 );
	if ( empty ( $url ) ){	
		$page_type = 'home';
	}
	else if ( substr( $url, -5 ) == '.html' ) {
		preg_match( '/([a-zA-Z0-9\-]+?\.html$)/', $url, $filtered );
		preg_match( '/(^[0-9]{8})/', $url, $uid );
		$page['uid'] = $uid[0];
		if ( ! $uid[0] ) {
			$page_type = 'static';			
		} else {
			$page_type = 'dynamic';
		}	
		$clean_url = $filtered[0];
	}
	else {
			
	}
	$page = get_finch_page_detail( $clean_url, $page_type );
	return $page;
}

function get_finch_page_detail( $clean_url, $page_type ){
	require_once( 'finch-data.php' );
	$page['dirs'] = get_finch_directory_data();
	$page['layout'] = get_page_layout_data();
	$items = get_finch_titles( $page );
	if ( $page_type == 'home' ){
		$page['home'] = $page_type == 'home' ? true : false;
		$page['url'] = str_replace( '.title', '.html', $items[0] );
		$page['stub'] = str_replace( '.title', '', $items[0] );
	}
	else {
		$page ['home'] = false;
		$page['url'] = $clean_url;
		preg_match( '/([a-zA-Z0-9\-\/]+)/', $clean_url, $match );
		$page['stub'] = $match[0];
	}
	preg_match( '/(^[0-9]{8})/', $page['stub'], $uid );
	$page['uid'] = $uid[0];
	$page['collate'] = collate_finch_files( $page );
	$page['header'] = get_finch_header( $page );
	$page['class'] = get_finch_class( $page );
	$page['name'] = get_finch_name( $page['stub'] );
	$page['title'] = get_finch_page_title( $page );
	$page['nav'] = get_finch_nav( $page, $items );
	$page['sidebar'] = get_finch_sidebar( $page, $items );
	$page['footer'] = get_finch_footer( $page, $items );
	if ( $page['collate']['image'] ) {
		list( $image['width'], $image['height'] ) = getimagesize( $page['collate']['image'] );
		$page['dir'] = $page['collate']['image'];
		$page['size'] = $image;
		$page['image'] = get_finch_image_tag( $page );
	}
	$page['html'] = $page['collate']['html'] ? get_finch_html( $page ) : '';
	if ( $page['collate']['pdf'] ) {
		load_finch_pdf_files();
		$page['pdf'] = get_finch_pdf( $page );
	} else {
		$page['pdf'] = '';
	}
	$page['video'] = $page['collate']['video'] ? get_finch_video_tag( $page ) : '';
	$page['audio'] = $page['collate']['audio'] ? get_finch_audio_tag( $page ) : '';
	return $page;
}

function load_finch_pdf_files(){
	$items = get_finch_pdf_data();
	if ( $items['load'] ){
		$file = $items['dir'] . '/' . $items['folder'] . '/' . $items['file'];
		require_once( $file );
	} else {
		return false;
	}
}

function get_page_not_found( $page, $items ){
	$page['file'] = $items[0];
	$page['url'] = str_replace( '.png', '.html', $page['file'] );
	$page['home'] = true;
	return $page;
}

function collate_finch_files( $page ){
	$collate['image'] = false;
	$collate['audio'] = false;
	$collate['video'] = false;
	$collate['pdf'] = false;
	$collate['html'] = false;

	if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['titles'] . '/' . $page['stub'] . '.title' ) ) {
		if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['videos'] . '/' . $page['stub'] . '.mp4' ) ) {
			$collate['video'] = $page['dirs']['uploads'] . '/' . $page['dirs']['videos'] . '/' . $page['stub'] . '.mp4';
		}
		else if ( strstr( $page['stub'], 'youtu' ) ) {
			$collate['video'] = 'youtu';
		}
		else if ( strstr( $page['stub'], 'vimeo' ) ) {
			$collate['video'] = 'vimeo';
		}
		else if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['images'] . '/' . $page['stub'] . '.png' ) ) {
			$collate['image'] = $page['dirs']['uploads'] . '/' . $page['dirs']['images'] . '/' . $page['stub'] . '.png';
		}
		else if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['images'] . '/' . $page['stub'] . '.png' ) ) {
			$collate['image'] = $page['dirs']['uploads'] . '/' . $page['dirs']['images'] . '/' . $page['stub'] . '.png';
		}
		else if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['pdfs'] . '/' . $page['stub'] . '.pdf' ) ) {
			$collate['pdf'] = $page['dirs']['uploads'] . '/' . $page['dirs']['pdfs'] . '/' . $page['stub'] . '.pdf';
		}
		else if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['html'] . '/' . $page['stub'] . '.html' ) ) {
			$collate['html'] = $page['dirs']['uploads'] . '/' . $page['dirs']['html'] . '/' . $page['stub'] . '.html';
		}
		else {}

		if ( file_exists( $page['dirs']['uploads'] . '/' . $page['dirs']['audio'] . '/' . $page['stub'] . '.mp3' ) ) {
			$collate['audio'] = $page['dirs']['uploads'] . '/' . $page['dirs']['audio'] . '/' . $page['stub'] . '.mp3';
		}
	}
	return $collate;
}

function get_finch_titles( $page ) {
	$handle = dirname(__FILE__) . '/' . $page['dirs']['uploads'] . '/'. $page['dirs']['titles'] . '/';
	foreach ( glob( $handle . "*.title" ) as $file ) {
		$arr[] = substr( strrchr( $file, '/' ), 1 );
	}
	return $arr;
}

function get_finch_dirs( $page ){
	$dir = dirname(__FILE__) . '/' . $page['dirs']['uploads'] . '/'. $page['dirs']['images'] . '/';
	$regex = "/^.*\.(png)$/";
	$rdi = new RecursiveDirectoryIterator( $dir );
	$rii = new RecursiveIteratorIterator( $rdi );
	$files = new RegexIterator( $rii, $regex, RegexIterator::GET_MATCH );
	$arr = array();
	foreach( $files as $file ) {
		$arr[] = $file[0];
	}
	return $arr;
}

function get_finch_dirs_rdi( $page ){
	//(\/[a-z]+\/)
	$path = dirname(__FILE__) . '/' . $page['dirs']['uploads'] . '/'. $page['dirs']['images'] . '/';
	$rii = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $path ),
			RecursiveIteratorIterator::SELF_FIRST);
	
	foreach( $rii as $file) {
		if( $file -> isDir()) {
			$arr[] = $file -> getRealpath();
		}
	}
}

function get_finch_file( $page, $items ){
	$search = str_replace( '.html', '.png', $page['url'] );
	$file =	$page['home'] ? $items[0] : $items[ array_search( $search , $items ) ];
	return $file;
}

function get_finch_page_url( $page ){
	if ( $page['stub'] ){
		$str = $page['stub'] . '.html';
		return $str;	
	}
	else {
		return false;
	}
}

function get_finch_image_tag( $page ){
	$image = get_finch_image_data();
	$image['alt'] = $page['file'];
	$image['class'] = $page['html'] ? 'adjust relative' : 'adjust absolute';
	$width = ! isset( $page['image']['width'] ) ||  $page['image']['width'] > 1600 ? 1600 : $page['image']['width']; 
	$height = ! isset( $page['image']['height'] ) || $page['image']['height'] > 900 ? 900 : $page['image']['height'];
	$str = sprintf( '<img id="image-plate" width="%s" height="%s" src="%s" class="%s" alt="%s"/>',
			$width,
			$height,
			$page['collate']['image'],
			$image['class'],
			$image['alt'],
			PHP_EOL
	);
	$image['tag'] = $str;
	return $image;
}

function get_finch_video_tag( $page ){
	$page = get_finch_video_short( $page );
	if ( strrpos ( $page['collate']['video'],  '.mp4', -3 ) ) {
		$str = get_finch_mp4_tag( $page );
		return $str;
	}
	else if ( isset ( $page['video']['type'] ) && $page['video']['type'] == 'youtu' ) {
		$str = get_finch_youtube_iframe( $page );
		return $str;
	} else if ( isset ( $page['video']['type'] ) && $page['video']['type'] == 'vimeo' ) {
		$str = get_finch_vimeo_iframe( $page );
		return $str;
	} else {
		return false;
	}
}

function get_finch_mp4_tag( $page ){
	$mp4 = $page['collate']['video'];
	$file = $page['dirs']['uploads'] . '/' . $page['dirs']['videos'] . '/' . $page['stub'] . '.webm';
	$webm = file_exists( $file ) ? $file : false;
	$items = get_finch_file_formats();
	$str = sprintf( '<video id="video" width="%s" height="%s" ', $page['width'], $page['height'] );
	$str .= 'controls="" ';
	$str .= ! empty( $page['image'] ) ? sprintf( 'poster="%s" >%s', $page['image']['url'], PHP_EOL ) : '';
	$str .= $items['mp4'] && $mp4 ? sprintf( '<source src="%s" type="video/mp4" />%s', $mp4 , PHP_EOL ) : '';
	$str .= $items['webm'] && $webm ? sprintf( '<source src="%s" type="video/webm" />%s', $webm, PHP_EOL ) : '';
	$str .= 'Your browser does not support the video tag.' . PHP_EOL;
	$str .= '</video>' . PHP_EOL;
	return $str;
}

function get_finch_video_short( $page ){
	if ( strstr( $page['title'], 'youtu' ) ) {
		$ex = explode( '-', $page['stub'] );
		$page['video']['type'] = 'youtu';
		$page['video']['short'] = isset( $ex[ count( $ex ) - 1 ] ) ? rtrim( $ex[ count( $ex ) - 1 ], '.title' ) : false;
	}
	else if ( strstr( $page['title'], 'vimeo' ) ) {
		$ex = explode( '-', $page['stub'] );
		$page['video']['type'] = 'vimeo';
		$page['video']['short'] = isset( $ex[ count( $ex ) - 1 ] ) ? rtrim( $ex[ count( $ex ) - 1 ], '.title' ) : false;
	}
	else{
		$page['video'] = false;
	}
	return $page;
}

function get_finch_video_url( $page, $type ){
	if ( $type == 'youtu' ) {
		$ex = explode( '-', $page['url'] );
		$short = isset( $ex[ count( $ex ) - 1 ] ) ? rtrim( $ex[ count( $ex ) - 1 ], '.html' ) : false;
	}
	else if ( $type == 'vimeo' ) {
		$ex = explode( '-', $page['url'] );
		$short = isset( $ex[ count( $ex ) - 1 ] ) ? rtrim( $ex[ count( $ex ) - 1 ], '.html' ) : false;
	}
	else{
		$short = false;
	}
	return $short;
}

function get_finch_youtube_iframe( $page ){
	$remote = get_finch_remote_video_urls();
	$str = sprintf( '<iframe id="remote-video" source src="%s/%s" ', $remote['youtube'], $page['video']['short'] );
	$str .= sprintf( 'width="%s" height="%s" ', $page['image']['width'], $page['image']['height'] );
	$str .= 'frameborder="0" allowfullscreen></iframe>' . PHP_EOL;
	return $str;
}

function get_finch_vimeo_iframe( $page ){
	$remote = get_finch_remote_video_urls();
	$str = sprintf( '<iframe  id="remote-video" src="%s/%s" ', $remote['vimeo'],  $page['video']['short'] );
	$str .= sprintf( 'width="%s" height="%s" ', $page['image']['width'], $page['image']['height'] );
	$str .= 'frameborder="0" allowfullscreen></iframe>' . PHP_EOL;
	return $str;
}

function get_finch_audio_tag( $page ){
	if ( $page['collate']['audio'] ) {
		return sprintf( '<audio id="audio" controls src="%s"></audio>%s', $page['collate']['audio'] , PHP_EOL );		
	} else {
		return false;
	}	
}

function get_finch_html( $page ){
	$html = get_html_stub( $page );
	if ( file_exists( $html ) ) {
		return file_get_contents( $html );
	} else {
		return false;
	}
}

function get_mp4_stub( $page ){
	return $page['dirs']['uploads'] . '/' . $page['dirs']['videos'] . '/' . $page['stub'] . '.mp4';
}

function get_webm_stub( $page ){
	return $page['dirs']['uploads'] . '/' . $page['dirs']['videos'] . '/' . $page['stub'] . '.webm'; 
}

function get_mp3_stub( $page ){
	return $page['dirs']['uploads'] . '/' . $page['dirs']['audio'] . '/' . $page['stub'] . '.mp3';	
}

function get_html_stub( $page ){
	return $page['dirs']['uploads'] . '/' . $page['dirs']['html'] . '/' . $page['stub'] . '.html';
}

function get_finch_class( $page ){
	return 'resize';
}

function get_finch_nav( $page, $items ) {
	$str = '';
	$lr = get_finch_lr( $page, $items );
	if ( $lr['left'] ) {
		$lr['left'] = $lr['left'] == $items[0] ? '/' : $lr['left']; 
		$str .= '<nav class="left-middle">' . PHP_EOL;
		$str .= sprintf( '<a href="%s" title="Prev"><span class="align-float-left mid-circle hover">&laquo;</span></a>', $lr['left'], PHP_EOL );
		$str .= '</nav>' . PHP_EOL;
	}
	if ( $lr['right'] ) {
		$str .= '<nav class="right-middle">' . PHP_EOL;
		$str .= sprintf( '<a href="%s" title="Next"><span class="align-float-right mid-circle hover">&raquo;</span></a>', $lr['right'] , PHP_EOL );
		$str .= '</nav>' . PHP_EOL;
	}
	return $str;
}

function get_finch_lr( $page, $items ){
	$search = $page['stub'];
	$match = array_filter( $items, function( $el ) use ( $search ) {
		return ( strpos( $el, $search ) !== false );
	});
	$key = key( $match );
	if ( isset ( $items[ $key - 1 ] )  ) {
		if ( $key - 1 == 0 ) {
			$lr['left'] = '/';
		}
		else {
			$lr['left'] = str_replace( '.title', '.html', $items[ $key - 1 ] );
		}
	}
	if ( isset ( $items[ $key + 1 ] )  ) {
		$lr['right'] = str_replace( '.title', '.html', $items[ $key + 1 ] );
	}
	return $lr;
}

function get_paging_buttons_numbered( $page, $items ){
	$str = '<span class="align-absolute-center">';
	if ( ! empty ( $items ) ){
		foreach ( $items as $k => $item ) {
		$str .= sprintf('<span class="tap-button"><a href="%s" title="%s">%s</a></span>%s', str_replace( '.title', '.html', $item ), get_finch_name( $item ), $k, PHP_EOL );		
		}
	$str .= '</span>';
	return $str;
	}
}

function get_paging_buttons_named( $page, $items ){
	$str = '';
	$max = 100;
	if ( ! empty ( $items ) ){
		foreach ( $items as $k => $item ) {
			if ( $k < $max ) {
				$str .= sprintf('<span class="tap-button"><a href="%s" title="%s">%s</a></span>%s', str_replace( '.title', '.html', $item ), get_finch_name( $item ), get_finch_name( $item ), PHP_EOL );
			}
			else {
				break;
			}
		}
		return $str;
	}
}

function get_finch_header( $page ){
	$header = get_finch_header_data();
	if ( $header['header'] ) {
	$str = '<header id="site-header">' . PHP_EOL;
	$str .= '<div class="inner">' . PHP_EOL;
	
	$str .= '</div>' . PHP_EOL;
	$str .= '</header>' . PHP_EOL;
	return $str;
	} else {
		return false;
	}
}

function get_finch_sidebar( $page, $items ){
	$site = get_finch_site_data();
	$sidebar = get_finch_sidebar_data();
	if ( $sidebar['sidebar'] ) {
		$horizontal = $sidebar['left'] ? ' left' : ' right';
		$vertical = $sidebar['bottom'] ? ' bottom' : ' top';
		$overlay = $sidebar['overlay'] ? ' overlay' : ' beside';
		$str = sprintf( '<section id="sidebar" class="sidebar position-absolute tablet-hide%s%s%s">%s', $horizontal, $vertical, $overlay, PHP_EOL );
		$str .= $sidebar['title'] ? sprintf( '<h1 class="negative-top-margin centered">%s</h1>%s', $site['title'], PHP_EOL ) : '';
		$str .= '<div class="inner">' . PHP_EOL;
		$str .= get_paging_buttons_named( $page, $items );
		$str .= '</div>' . PHP_EOL; //innner
		$str .= get_finch_tap_grid( $page );
		$str .= '</section>' . PHP_EOL;
		$str .= sprintf( '<section id="handle" class="handle%s%s tap-button button gradient" title="Click to show pages">', $horizontal, $vertical );
		$str .= '<span class="laptop-show rotate-90 opaque">III</span>';
		$str .= '<span class="laptop-hide opaque">Pages</span>';
		$str .= '</section>' . PHP_EOL;
		return $str;
	} else {
		return false;
	}
}

function get_finch_static_menu_items( $page ) {
	$items = get_finch_static_menu_data();
	if( $items['static'] ){
		$str = '<span id="static-menu" class="">';
		if ( ! empty ( $items )){
			$cnt = 0;
			foreach ( $items as $key => $item ){
				if ( $cnt > 0 ) {
					$file = $cnt == 0 ? '/' : $key . '.html';
					$str .= sprintf('<span class="tap-button"><a href="%s" title="%s">%s</a></span>%s', $file , get_name_from_stub( $key ), get_name_from_stub( $key ), PHP_EOL );
				}
				$cnt++;
			}
			$str .= '</span>';
			return $str;
			}
			else {
				return false;
			}
	}		
	else {
		return false;
	}
}

function get_finch_footer( $page, $items ){
	$footer = get_finch_footer_data();
	if ( $footer['footer'] ){
		$str = '<footer id="site-footer">' . PHP_EOL;
		$str .= '<div class="inner">' . PHP_EOL;
		$str .= '<span class="align-absolute-center">';
		$str .= get_finch_static_menu_items( $page );
		$str .= '</span>';
		$str .= '</div>' . PHP_EOL;
		$str .= '</footer>' . PHP_EOL;
		return $str;
	} else {
		return false;
	}
}

function get_finch_name ( $file ) {
	preg_match( '/([0-9a-z\-\/]+)/', $file, $match );
	$fname = $match[0];
	$ex2 = explode ( "-", $fname );
	$cnt = count ( $ex2 );
	$i = 0;
	$str = "";
	$caps = get_no_caps_data();
	if ( ! empty ( $ex2 ) ) foreach ( $ex2 as $item ) {
		if ( $i !== 0 ) {
			if ( ! in_array( $item, $caps ) || $i == 1 ) {
				$str .= ucfirst ( $item );
			} else {
				$str .= $item;
			}
		}
		if ( $i < $cnt ) {
			$str .= " ";
		}
		$i++;
	}
	return trim( $str );	
}

function get_name_from_stub ( $stub ) {
	$ex = explode ( "-", $stub );
	$cnt = count ( $ex );
	$i = 0;
	$str = "";
	$caps = get_no_caps_data();
	if ( ! empty ( $ex ) ) foreach ( $ex as $item ) {
		if ( ! in_array( $item, $caps ) || $i == 1 ) {
			$str .= ucfirst ( $item );
		} else {
			$str .= $item;
		}
		if ( $i < $cnt ) {
			$str .= " ";
		}
		$i++;
	}
	return trim( $str );
}

function get_finch_page_title ( $page ) {
	if ( empty( $page['url'] ) ){
		$title = sprintf( '<h4 id="page-title" class="title negative-top-margin">%s</h4>', $page['name'] );
		return $title;
	} else {
		$title = sprintf( '<h4 id="page-title" class="title negative-top-margin"><a href="%s">%s</a></h4>', $page['url'], $page['name'] );
		return $title;
	}
}

function get_site_title(){
	global $page;
	$site = get_finch_site_data();
	$str = sprintf( '%s%s%s', $site['title'], ' | ', $site['description'] );
	return $str;
}

function is_finch_home( $path ){
	if( $path == '/' ) {
		return true;
	}
	else {
		return false;
	}
}

function get_finch_tap_grid( $page ) {
	$items = get_finch_tap_grid_data();
	$str = '';
	$str .= sprintf( '<div id="tap-grid" class="tap-grid position-absolute bottom-right">%s', PHP_EOL );
	$str .= '<div class="inner">' . PHP_EOL;
	$str .= sprintf( '<a href="%s"><div id="home" class="unit size1of2 icon icon-home" title="%s"><div class="inner"></div></div></a>%s', $items['home']['value'], $items['home']['name'], PHP_EOL );
	$str .= sprintf( '<div id="phone-click" class="unit size1of2 icon icon-handset display-click" title="%s"><div class="inner"></div></div>%s', $items['phone']['name'], PHP_EOL );
	$str .= sprintf( '<div id="email-click" class="unit size1of2 icon icon-mail display-click" title="%s"><div class="inner"></div></div>%s', $items['email']['name'], PHP_EOL );
	$str .= sprintf( '<div id="share-click" class="unit size1of2 icon icon-share display-click" title="%s"><div class="inner"></div></div>%s', ucfirst( key( $items['share'] ) ), PHP_EOL );
	$str .= '</div>' . PHP_EOL;
	$str .= get_finch_tap_grid_items( $page );
	$str .= '</div>' . PHP_EOL;
	return $str;
}

function get_finch_tap_grid_items( $page ) {
	$items = get_finch_tap_grid_data();
	if (! empty ( $items ) ) {
		$str = '';
		$str .= sprintf( '<div id="%s" class="fixed-center hide">%s</div>%s', 'phone', $items['phone']['value'], PHP_EOL );
		$str .= sprintf( '<div id="%s" class="fixed-center hide">%s</div>%s', 'email', $items['email']['value'], PHP_EOL );
		$str .= get_finch_share_items( 'share', $items['share'] );
		return $str;		
	} else {
		return false;
	}
}

function get_finch_share_items( $name, $items ){
	if ( ! empty( $items ) ) {
		$str = sprintf( '<div id="%s" class="fixed-center hide">%s', $name , PHP_EOL );
		foreach ( $items as $item ){
			if ( $item['url'] != '0' ) {
				$str .= sprintf( '<a href="%s" target="_blank" title="%s"><span class="icon icon-%s"></span></a>%s', $item['url'], $item['url'], $item['name'],  PHP_EOL );
			}
		}
		$str .= '</div>' . PHP_EOL;
		return $str;
	}
}

function get_finch_plugin_stylesheets(){
	$items = get_finch_plugin_data();
	$dirs = get_finch_directory_data();
	if ( ! empty( $items ) ) foreach ( $items as $item ) {
		$str = '';
		if ( $item['load'] ){
			$str .= sprintf( '<style></style>%s', PHP_EOL );
		}
		return $str;
	} else {
		return false;
	}
}

function get_finch_plugin_scripts(){
	$items = get_finch_plugin_data();
	$dirs = get_finch_directory_data();
	if ( ! empty( $items ) ) foreach ($items as $item ) {
		$str = '';
		if ( $item['load'] ){
			$str .= sprintf( '<style></style>%s', PHP_EOL );
		}
	} else {
		return false;
	}
}

function get_finch_pdf( $page ){
	if ( file_exists ( $page['collate']['pdf'] ) ) {
		if ( function_exists( 'get_finch_pdf_html' ) ){
			$str = get_finch_pdf_html( $page );
			return $str;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function get_finch_pdf_stylesheets() {
	$str = '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . PHP_EOL;
	$str .= '<link rel="stylesheet" href="plugins/pdf-viewer/css/viewer.min.css"/>' . PHP_EOL;
	return $str;
}

function get_finch_pdf_scripts( $page ) {
	$file = $page['dirs']['uploads'] . '/' . $page['dirs']['pdfs'] . '/' . $page['stub'] . '.pdf';
	$url = sprintf( "var DEFAULT_URL = '%s';", $file );
	$str = sprintf( '<script type="text/javascript">%s</script>%s', $url, PHP_EOL );
	$str .= '<script src="plugins/pdf-viewer/js/compatibility.min.js" defer></script>' . PHP_EOL;
	$str .= '<script src="plugins/pdf-viewer/js/pdf.min.js" defer></script>' . PHP_EOL;
	$str .= '<script src="plugins/pdf-viewer/js/l10n.min.js" defer></script>' . PHP_EOL;
	$str .= '<script src="plugins/pdf-viewer/js/viewer.min.js" defer></script>' . PHP_EOL;
	return $str;
}

function pre_dump( $arr ){
	echo '<pre>';
	var_dump( $arr );
	echo '</pre>';
}
