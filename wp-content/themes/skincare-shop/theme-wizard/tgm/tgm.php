<?php require get_template_directory() . '/theme-wizard/tgm/class-tgm-plugin-activation.php';

function skincare_shop_register_recommended_plugins() {
	$plugins = array(
		array(
			'name'             => __( 'WooCommerce', 'skincare-shop' ),
			'slug'             => 'woocommerce',
			'required'         => false,
			'force_activation' => false,
		),
		array(
			'name'             => __( 'WPC Smart Wishlist for WooCommerce', 'skincare-shop' ),
			'slug'             => 'woo-smart-wishlist',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
		array(
			'name'             => __( 'Classic Widgets', 'skincare-shop' ),
			'slug'             => 'classic-widgets',
			'source'           => '',
			'required'         => false,
			'force_activation' => false,
		),
	);
	$config = array();
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'skincare_shop_register_recommended_plugins' );