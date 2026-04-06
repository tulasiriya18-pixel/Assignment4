<?php

$skincare_shop_custom_css = "";

$skincare_shop_primary_color = get_theme_mod('skincare_shop_primary_color');
$skincare_shop_second_color = get_theme_mod('skincare_shop_second_color');
$skincare_shop_third_color = get_theme_mod('skincare_shop_third_color');

/*------------------ Primary Global Color -----------*/

if ($skincare_shop_primary_color) {
  $skincare_shop_custom_css .= ':root {';
  $skincare_shop_custom_css .= '--primary-color: ' . esc_attr($skincare_shop_primary_color) . ' !important;';
  $skincare_shop_custom_css .= '} ';
}

/*------------------ Secondary Global Color -----------*/

if ($skincare_shop_second_color) {
  $skincare_shop_custom_css .= ':root {';
  $skincare_shop_custom_css .= '--secondary-color: ' . esc_attr($skincare_shop_second_color) . ' !important;';
  $skincare_shop_custom_css .= '} ';
}

/*------------------ Tertiary Global Color -----------*/

if ($skincare_shop_third_color) {
  $skincare_shop_custom_css .= ':root {';
  $skincare_shop_custom_css .= '--tertiary-color: ' . esc_attr($skincare_shop_third_color) . ' !important;';
  $skincare_shop_custom_css .= '} ';
}

/*-------------------- Scroll Top Alignment-------------------*/

$skincare_shop_scroll_top_alignment = get_theme_mod( 'skincare_shop_scroll_top_alignment','right-align');

if($skincare_shop_scroll_top_alignment == 'right-align'){
$skincare_shop_custom_css .='#button{';
	$skincare_shop_custom_css .='right: 3%;';
$skincare_shop_custom_css .='}';
}else if($skincare_shop_scroll_top_alignment == 'center-align'){
$skincare_shop_custom_css .='#button{';
	$skincare_shop_custom_css .='right:0; left:0; margin: 0 auto;';
$skincare_shop_custom_css .='}';
}else if($skincare_shop_scroll_top_alignment == 'left-align'){
$skincare_shop_custom_css .='#button{';
	$skincare_shop_custom_css .='left: 3%;';
$skincare_shop_custom_css .='}';
}

/*-------------------- Archive Page Pagination Alignment-------------------*/

$skincare_shop_archive_pagination_alignment = get_theme_mod( 'skincare_shop_archive_pagination_alignment','left-align');

if($skincare_shop_archive_pagination_alignment == 'right-align'){
$skincare_shop_custom_css .='.pagination{';
	$skincare_shop_custom_css .='justify-content: end;';
$skincare_shop_custom_css .='}';
}else if($skincare_shop_archive_pagination_alignment == 'center-align'){
$skincare_shop_custom_css .='.pagination{';
	$skincare_shop_custom_css .='justify-content: center;';
$skincare_shop_custom_css .='}';
}else if($skincare_shop_archive_pagination_alignment == 'left-align'){
$skincare_shop_custom_css .='.pagination{';
	$skincare_shop_custom_css .='justify-content: start;';
$skincare_shop_custom_css .='}';
}

/*-------------------- Scroll Top Responsive-------------------*/

$skincare_shop_resp_scroll_top = get_theme_mod( 'skincare_shop_resp_scroll_top',true);
if($skincare_shop_resp_scroll_top == true && get_theme_mod( 'skincare_shop_scroll_to_top',true) != true){
	$skincare_shop_custom_css .='#button.show{';
		$skincare_shop_custom_css .='visibility:hidden !important;';
	$skincare_shop_custom_css .='} ';
}
if($skincare_shop_resp_scroll_top == true){
	$skincare_shop_custom_css .='@media screen and (max-width:575px) {';
	$skincare_shop_custom_css .='#button.show{';
		$skincare_shop_custom_css .='visibility:visible !important;';
	$skincare_shop_custom_css .='} }';
}else if($skincare_shop_resp_scroll_top == false){
	$skincare_shop_custom_css .='@media screen and (max-width:575px){';
	$skincare_shop_custom_css .='#button.show{';
		$skincare_shop_custom_css .='visibility:hidden !important;';
	$skincare_shop_custom_css .='} }';
}

/*-------------------- Preloader Responsive-------------------*/

$skincare_shop_resp_loader = get_theme_mod('skincare_shop_resp_loader',false);
if($skincare_shop_resp_loader == true && get_theme_mod('skincare_shop_header_preloader',false) == false){
	$skincare_shop_custom_css .='@media screen and (min-width:575px){
		.preloader{';
		$skincare_shop_custom_css .='display:none !important;';
	$skincare_shop_custom_css .='} }';
}

if($skincare_shop_resp_loader == false){
	$skincare_shop_custom_css .='@media screen and (max-width:575px){
		.preloader{';
		$skincare_shop_custom_css .='display:none !important;';
	$skincare_shop_custom_css .='} }';
}

// Scroll to top button shape 

$skincare_shop_scroll_border_radius = get_theme_mod( 'skincare_shop_scroll_to_top_radius','curved-box');
if($skincare_shop_scroll_border_radius == 'box'){
	$skincare_shop_custom_css .='#button{';
		$skincare_shop_custom_css .='border-radius: 0px;';
	$skincare_shop_custom_css .='}';
}else if($skincare_shop_scroll_border_radius == 'curved-box'){
	$skincare_shop_custom_css .='#button{';
		$skincare_shop_custom_css .='border-radius: 4px;';
	$skincare_shop_custom_css .='}';
}
else if($skincare_shop_scroll_border_radius == 'circle'){
	$skincare_shop_custom_css .='#button{';
		$skincare_shop_custom_css .='border-radius: 50%;';
	$skincare_shop_custom_css .='}';
}

// Footer Background Image Attachment 

$skincare_shop_footer_attachment = get_theme_mod( 'skincare_shop_background_attachment','scroll');
if($skincare_shop_footer_attachment == 'fixed'){
	$skincare_shop_custom_css .='.site-footer{';
		$skincare_shop_custom_css .='background-attachment: fixed;';
	$skincare_shop_custom_css .='}';
}elseif ($skincare_shop_footer_attachment == 'scroll'){
	$skincare_shop_custom_css .='.site-footer{';
		$skincare_shop_custom_css .='background-attachment: scroll;';
	$skincare_shop_custom_css .='}';
}

// Menu Hover Style	

$skincare_shop_menus_item = get_theme_mod( 'skincare_shop_menus_style','None');
if($skincare_shop_menus_item == 'None'){
	$skincare_shop_custom_css .='#site-navigation .menu ul li a:hover, .main-navigation .menu li a:hover{';
		$skincare_shop_custom_css .='';
	$skincare_shop_custom_css .='}';
}else if($skincare_shop_menus_item == 'Zoom In'){
	$skincare_shop_custom_css .='#site-navigation .menu ul li a:hover, .main-navigation .menu li a:hover{';
		$skincare_shop_custom_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important;';
	$skincare_shop_custom_css .='}';

	$skincare_shop_custom_css .= '.main-navigation ul ul li a:hover {';
	$skincare_shop_custom_css .= 'margin-left: 20px;';
	$skincare_shop_custom_css .= '}';
}	
