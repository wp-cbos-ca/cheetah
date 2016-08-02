<?php

defined( 'FINCH' ) || die();

function get_finch_site_data(){
	$items = array(
		'title' => 'The Finch',
		'description' => 'A Lightweight, Fiesty PHP Driven Platform for Your Ideas',
	);
	return $items;
}

function get_finch_header_data(){
	$items = array(
			'header' => 0,
			'tablet' => 1,
			'mobile' => 1,
	);
	return $items;
}

function get_page_layout_data(){
	$items = array(
		'sidebar' => 1,
		'mobile-header-show' => 0,
		'tablet-header-show' => 0,
		'sidebar-overlay' => 1,
	);
	return $items;
}

function get_finch_sidebar_data(){
	$items = array(
			'sidebar' => 1,
			'title' => 0,
			'left' => 0,
			'bottom' => 1,
			'overlay' => 1,
			'button' => 1,
			'slide' => 0,
	);
	return $items;
}

function get_finch_footer_data(){
	$items = array(
		'footer' => 1,
		'tablet' => 1,
		'mobile' => 1,
	);
	return $items;
}

function get_finch_static_menu_data(){
	$items = array(
		'static' => 0,
		'item-1' => 1,
		'item-2' => 1,
		'item-3' => 1,
		'item-4' => 1,
	);
	return $items;
}

function get_finch_image_data(){
	$items = array(
		'width' => '1600',
		'height' => '900',
		'ext' => 'png',
	);
	return $items;
}

function get_no_caps_data(){
	return array( 'the', 'in', 'at', 'is', 'by' );
}

function get_finch_directory_data(){
	$items = array(
		'uploads' => 'uploads', 
		'static' => 'static',
		'titles' => 'titles',
		'images' => 'images',
		'videos' => 'videos',
		'audio' => 'audio',
		'html' => 'html',
		'pdfs' => 'pdfs',
		'plugins' => 'plugins',
	);
	return $items;
}

function get_finch_file_formats(){
	$items = array(
		'mp3' => true,
		'mp4' => true,
		'webm' => false,
		'vimeo' => true,
		'youtube' => true,
	);
	return $items;
}

function get_finch_remote_video_urls(){
	$items = array(
	'youtube' => 'https://www.youtube.com/embed',
	'vimeo' => 'https://player.vimeo.com/video',
	);
	return $items;
}

function get_finch_rss_data(){
	$items = array(
			'url' => '',
	);
	return $items;
}

function get_finch_tap_grid_data(){
	$items = array(
			'home' => array( 'name' => 'Home', 'value' => '/' ),
			'phone' => array( 'name' => 'Phone', 'value' => '(519) 804-5543' ),
			'email' => array( 'name' =>  'Email', 'value' => 'cbos@tnoep.ca' ),
			'share' => array (
				array( 'name' => 'linkedin', 'url' => 'https://ca.linkedin.com/in/clarencebos' ),
				array( 'name' => 'facebook', 'url' => '0' ),
				array( 'name' => 'googleplus', 'url' => '0' ),
				array( 'name' => 'twitter', 'url' => '0' ),
			),
	);
	return $items;
}

function get_finch_tap_grid_full_data(){
	$items = array(
		 0 => 'A',  1 => 'B',  2 => 'C',  3 => '',
		 4 => '1',  5 => '2',  6 => '3',  7 => '',
		 8 => '4',  9 => '5', 10 => '6', 11 => '',
		12 => '7', 13 => '8', 14 => '9', 15 => '',
		16 => '',  17 => '0',  18 => '',  19 => '',
	);
	return $items;
}

function get_finch_pdf_data(){
	$items = array( 'dir' => 'plugins', 'folder' => 'pdf-viewer', 'file' => 'plugin.php', 'load' => 1 );
	return $items;
}

function get_pay_button_html(){
	//email link ($10 CAD).
	//https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QWH4S9SFXT3MY
	$str = '<div style="text-align: center;">' . PHP_EOL;
	$str .= '<p style="text-align: center;">Donate $10</p>' . PHP_EOL;
	$str .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="width: 125px;">';
	$str .= '<input type="hidden" name="cmd" value="_s-xclick">';
	$str .= '<input type="hidden" name="hosted_button_id" value="QWH4S9SFXT3MY">';
	$str .= '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" style="border: 0;">';
	$str .= '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" style="border: 0; display: none;">';
	$str .= '</form>' . PHP_EOL;
	$str .= '</div>' . PHP_EOL;
	return $str;
}
