<?php
/**
 * Skincare Shop functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package skincare_shop
 */

if ( ! defined( 'SKINCARE_SHOP_URL' ) ) {
    define( 'SKINCARE_SHOP_URL', esc_url( 'https://www.themeignite.com/products/skincare-wordpress-theme', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_FREE_DOC_URL' ) ) {
    define( 'SKINCARE_SHOP_FREE_DOC_URL', esc_url( 'https://demo.themeignite.com/documentation/skincare-shop-free/', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_PRO_DOC_URL' ) ) {
    define( 'SKINCARE_SHOP_PRO_DOC_URL', esc_url( 'https://demo.themeignite.com/documentation/skincare-shop-pro/', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_DEMO_URL' ) ) {
    define( 'SKINCARE_SHOP_DEMO_URL', esc_url( 'https://demo.themeignite.com/skincare-shop/', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_REVIEW_URL' ) ) {
    define( 'SKINCARE_SHOP_REVIEW_URL', esc_url( 'https://wordpress.org/support/theme/skincare-shop/reviews/#new-post', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_SUPPORT_URL' ) ) {
    define( 'SKINCARE_SHOP_SUPPORT_URL', esc_url( 'https://wordpress.org/support/theme/skincare-shop/', 'skincare-shop') );
}
if ( ! defined( 'SKINCARE_SHOP_BUNDLE_URL' ) ) {
    define( 'SKINCARE_SHOP_BUNDLE_URL', esc_url( 'https://www.themeignite.com/products/wp-theme-bundle', 'skincare-shop') );
}

$skincare_shop_theme_data = wp_get_theme();
if( ! defined( 'SKINCARE_SHOP_THEME_VERSION' ) ) define ( 'SKINCARE_SHOP_THEME_VERSION', $skincare_shop_theme_data->get( 'Version' ) );
if( ! defined( 'SKINCARE_SHOP_THEME_NAME' ) ) define( 'SKINCARE_SHOP_THEME_NAME', $skincare_shop_theme_data->get( 'Name' ) );

if ( ! function_exists( 'skincare_shop_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function skincare_shop_setup() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'skincare-shop' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
        'status',
        'audio', 
        'chat'
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'skincare_shop_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	/* Custom Logo */
    add_theme_support( 'custom-logo', array(
    	'header-text' => array( 'site-title', 'site-description' ),
    ) );

	add_theme_support( 'woocommerce' );

    load_theme_textdomain( 'skincare-shop', get_template_directory() . '/languages' );

    /**
	 * Custom template tags for this theme.
	 */
	require get_template_directory() . '/inc/template-tags.php';

	/**
	 * Custom functions that act independently of the theme templates.
	 */
	require get_template_directory() . '/inc/extra.php';

	/**
	 * Customizer additions.
	 */
	require get_template_directory() . '/inc/customizer.php';

	/**
	 * Social Links Widget
	 */
	require get_template_directory() . '/inc/widget-social-links.php';

	/**
	 * Info Theme
	 */
	require get_template_directory() . '/inc/info.php';

	/**
	 * Info Theme
	 */
	require get_template_directory() . '/inc/sanitization.php';

	/**
	 * Getting Started
	*/
	require get_template_directory() . '/inc/getting-started/getting-started.php';

	/**
	 * setup wizard
	 */
	require get_parent_theme_file_path( '/theme-wizard/config.php' );
}
endif;
add_action( 'after_setup_theme', 'skincare_shop_setup' );

	/**
	 * Implement the Custom Header feature.
	 */
	require get_template_directory() . '/inc/custom-header.php';

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $skincare_shop_content_width
 */
function skincare_shop_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'skincare_shop_content_width', 780 );
}
add_action( 'after_setup_theme', 'skincare_shop_content_width', 0 );


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function skincare_shop_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Option', 'skincare-shop' ),
		'id'            => 'right-sidebar',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Two', 'skincare-shop' ),
		'id'            => 'sidebar-2',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Three', 'skincare-shop' ),
		'id'            => 'sidebar-3',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer One', 'skincare-shop' ),
		'id'            => 'footer-one',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Two', 'skincare-shop' ),
		'id'            => 'footer-two',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Three', 'skincare-shop' ),
		'id'            => 'footer-three',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Four', 'skincare-shop' ),
		'id'            => 'footer-four',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop Sidebar', 'skincare-shop' ),
		'id'            => 'woocommerce-sidebar',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Single Product Sidebar', 'skincare-shop' ),
		'id'            => 'woocommerce-single-sidebar',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Header Sidebar', 'skincare-shop' ),
		'id'            => 'header-sidebar',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'skincare_shop_widgets_init' );

// Change number of products per row to 3
add_filter('loop_shop_columns', 'skincare_shop_loop_columns');
if (!function_exists('skincare_shop_loop_columns')) {
    function skincare_shop_loop_columns() {
        $skincare_shop_colm = 3;
        return $skincare_shop_colm;
    }
}

if( ! function_exists( 'skincare_shop_scripts' ) ) :

/**
 * Enqueue scripts and styles.
 */
function skincare_shop_scripts() {

	// Use minified libraries if SCRIPT_DEBUG is false
    $skincare_shop_build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $skincare_shop_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    wp_enqueue_style( 'bootstrap-style', get_template_directory_uri().'/css/build/bootstrap.css' );

    wp_enqueue_style( 'fontawesome-all', esc_url(get_template_directory_uri()).'/css/all.min.css');

	wp_enqueue_style( 'fontawesome-all', esc_url(get_template_directory_uri()).'/css/all.css');

    wp_enqueue_script('skincare-shop-js-cookie');

	wp_enqueue_style( 'skincare-shop-style', get_stylesheet_uri(), array(), SKINCARE_SHOP_THEME_VERSION );

	require get_parent_theme_file_path( '/inc/css_custom.php' );
	wp_add_inline_style( 'skincare-shop-style',$skincare_shop_custom_css );

	wp_style_add_data('skincare-shop-basic-style', 'rtl', 'replace');

  	wp_enqueue_script( 'skincare-shop-all', get_template_directory_uri() . '/js' . $skincare_shop_build . '/all' . $skincare_shop_suffix . '.js', array( 'jquery' ), '6.1.1', true );
  	wp_enqueue_script( 'skincare-shop-v4-shims', get_template_directory_uri() . '/js' . $skincare_shop_build . '/v4-shims' . $skincare_shop_suffix . '.js', array( 'jquery' ), '6.1.1', true );
  	wp_enqueue_script( 'skincare-shop-modal-accessibility', get_template_directory_uri() . '/js' . $skincare_shop_build . '/modal-accessibility' . $skincare_shop_suffix . '.js', array( 'jquery' ), SKINCARE_SHOP_THEME_VERSION, true );
	wp_enqueue_script( 'skincare-shop-js', get_template_directory_uri() . '/js/build/custom.js', array('jquery'), SKINCARE_SHOP_THEME_VERSION, true );
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/build/bootstrap.js', array('jquery'), SKINCARE_SHOP_THEME_VERSION, true );

	// Wow script.
	wp_enqueue_script( 'wow-jquery', get_template_directory_uri() . '/js/build/wow.js', array('jquery'),'' ,true );
	// Animate CSS
	wp_enqueue_style( 'animate-style', get_template_directory_uri() . '/css/build/animate.css' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'skincare_shop_scripts' );

if( ! function_exists( 'skincare_shop_admin_scripts' ) ) :
/**
 * Addmin scripts
*/
function skincare_shop_admin_scripts() {
	wp_enqueue_style( 'skincare-shop-admin-style',get_template_directory_uri().'/inc/css/admin.css', SKINCARE_SHOP_THEME_VERSION, 'screen' );
}
endif;
add_action( 'admin_enqueue_scripts', 'skincare_shop_admin_scripts' );

function skincare_shop_customize_enque_js(){
	wp_enqueue_script( 'customizer', get_template_directory_uri() . '/inc/js/customizer.js', array('jquery'), '2.6.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'skincare_shop_customize_enque_js', 0 );


if( ! function_exists( 'skincare_shop_block_editor_styles' ) ) :
/**
 * Enqueue editor styles for Gutenberg
 */
function skincare_shop_block_editor_styles() {
	// Use minified libraries if SCRIPT_DEBUG is false
	$skincare_shop_build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
	$skincare_shop_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	
	// Block styles.
	wp_enqueue_style( 'skincare-shop-block-editor-style', get_template_directory_uri() . '/css' . $skincare_shop_build . '/editor-block' . $skincare_shop_suffix . '.css' );
}
endif;
add_action( 'enqueue_block_editor_assets', 'skincare_shop_block_editor_styles' );

/**
 * Remove header text setting and control from the Customizer.
 */
function skincare_shop_remove_customizer_setting($wp_customize) {
    // Replace 'your_setting_id' with the actual ID or name of the setting you want to remove
    $wp_customize->remove_control('display_header_text');
    $wp_customize->remove_setting('display_header_text');
}
add_action('customize_register', 'skincare_shop_remove_customizer_setting');

function skincare_shop_custom_blog_banner_title() {
    if (is_404()) {
        echo '<h1 class="entry-title">'. esc_html__( 'Oops! That page can’t be found.', 'skincare-shop' ).'</h1>';
    } elseif (is_search()) {
        echo '<h1 class="entry-title">'. esc_html__( 'Search Result For.', 'skincare-shop' ).' ' . get_search_query() . '</h1>';
    } elseif (is_home() && !is_front_page()) {
        echo '<h1 class="entry-title">'. esc_html__( 'Blogs', 'skincare-shop' ).'</h1>';
    } elseif (function_exists('is_shop') && is_shop()) {
        echo '<h1 class="entry-title">'. esc_html__( 'Shop', 'skincare-shop' ).'</h1>';
    } elseif (is_page()) {
        the_title('<h1 class="entry-title">', '</h1>');
    } elseif (is_single()) {
        the_title('<h1 class="entry-title">', '</h1>');
    } elseif (is_archive()) {
        the_archive_title('<h1 class="entry-title">', '</h1>');
    } else {
        the_archive_title('<h1 class="entry-title">', '</h1>');
    }
	skincare_shop_the_breadcrumb();
}

function skincare_shop_the_breadcrumb() {
    echo '<div class="breadcrumb justify-content-center align-items-center mt-5">';

    if (!is_home()) {
        echo '<a class="home-main align-self-center" href="' . esc_url(home_url()) . '">';
        bloginfo('name');
        echo '</a> >> ';

        // WooCommerce pages handling
        if (function_exists('is_woocommerce') && is_woocommerce()) {
            // Shop Page
            if (is_shop()) {
                echo '<span class="current-breadcrumb">' . esc_html(get_the_title(wc_get_page_id('shop'))) . '</span>';

            // Product Category
            } elseif (is_product_category()) {
                $term = get_queried_object();
                echo '<a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">' . esc_html(get_the_title(wc_get_page_id('shop'))) . '</a> >> ';
                echo '<span class="current-breadcrumb">' . esc_html($term->name) . '</span>';

            // Single Product
            } elseif (is_product()) {
                echo '<a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">' . esc_html(get_the_title(wc_get_page_id('shop'))) . '</a> >> ';
                echo '<span class="current-breadcrumb">' . esc_html(get_the_title()) . '</span>';
            }

        // Regular category/single/page
        } elseif (is_category() || is_single()) {
            the_category(' >> ');
            if (is_single()) {
                echo ' >> <span class="current-breadcrumb">' . esc_html(get_the_title()) . '</span>';
            }
        } elseif (is_page()) {
            echo '<span class="current-breadcrumb">' . esc_html(get_the_title()) . '</span>';
        }
    }

    echo '</div>';
}

function skincare_shop_enqueue_google_fontss() {
    $skincare_shop_heading_font_family = get_theme_mod('skincare_shop_heading_font_family', '');
    $skincare_shop_body_font_family = get_theme_mod('skincare_shop_body_font_family', '');

    // Google Fonts URL builder
    $google_fonts = array(
        'Arial'          => '',
        'Verdana'        => '',
        'Helvetica'      => '',
        'Times New Roman'=> '',
        'Georgia'        => '',
        'Courier New'    => '',
        'Trebuchet MS'   => '',
        'Tahoma'         => '',
        'Palatino'       => '',
        'Garamond'       => '',
        'Impact'         => '',
        'Comic Sans MS'  => '',
        'Lucida Sans'    => '',
        'Arial Black'    => '',
        'Gill Sans'      => '',
        'Segoe UI'       => '',
        'Open Sans'      => 'Open+Sans:wght@400;700',
        'Outfit'         => 'Outfit:wght@400;700',
        'Lato'           => 'Lato:wght@400;700',
        'Montserrat'     => 'Montserrat:wght@400;700',
        'Libre Baskerville' => 'Libre+Baskerville:wght@400;700'
    );

    $skincare_shop_google_fonts_url = '';

    if (!empty($google_fonts[$skincare_shop_heading_font_family]) || !empty($google_fonts[$skincare_shop_body_font_family])) {
        $fonts = array();

        if (!empty($google_fonts[$skincare_shop_heading_font_family])) {
            $fonts[] = $google_fonts[$skincare_shop_heading_font_family];
        }

        if (!empty($google_fonts[$skincare_shop_body_font_family])) {
            $fonts[] = $google_fonts[$skincare_shop_body_font_family];
        }

        // Build Google Fonts URL
        $skincare_shop_google_fonts_url = add_query_arg(
            'family',
            implode('|', $fonts),
            'https://fonts.googleapis.com/css2'
        );
    }

    if ($skincare_shop_google_fonts_url) {
        wp_enqueue_style('skincare-shop-google-fonts', $skincare_shop_google_fonts_url, false);
    }
}
add_action('wp_enqueue_scripts', 'skincare_shop_enqueue_google_fontss');


/*-----------------------Typography Function---------------------------------------*/

function skincare_shop_apply_typography() {
    $skincare_shop_heading_font_family = get_theme_mod('skincare_shop_heading_font_family');
    $skincare_shop_body_font_family = get_theme_mod('skincare_shop_body_font_family');

    $skincare_shop_custom_css = '';

    if ($skincare_shop_body_font_family) {
        $skincare_shop_custom_css .= "body, a, a:active, a:hover { font-family: " . esc_html($skincare_shop_body_font_family) . " !important; }";
    }

    if ($skincare_shop_heading_font_family) {
        $skincare_shop_custom_css .= "h1, h2, h3, h4, h5, h6 { font-family: " . esc_html($skincare_shop_heading_font_family) . " !important; }";
    }

    if (!empty($skincare_shop_custom_css)) {
        wp_add_inline_style('skincare-shop-style', $skincare_shop_custom_css);
    }
}
add_action('wp_enqueue_scripts', 'skincare_shop_apply_typography');


/*-----------------------Menu Typography Start---------------------------------------*/

function skincare_shop_menu_customizer_css() {
    $skincare_shop_menu_font_weight = get_theme_mod('skincare_shop_menu_font_weight', '600');
    $skincare_shop_menu_text_transform = get_theme_mod('skincare_shop_menu_text_transform', 'capitalize');

    $skincare_shop_custom_css = "
        .main-navigation ul li a {
            font-weight: " . esc_html($skincare_shop_menu_font_weight) . ";
            text-transform: " . esc_html($skincare_shop_menu_text_transform) . ";
        }
    ";

    wp_add_inline_style('skincare-shop-style', $skincare_shop_custom_css);
}
add_action('wp_enqueue_scripts', 'skincare_shop_menu_customizer_css');

/*-----------------------Menu Typography End---------------------------------------*/

/**
 * AJAX handler to dismiss Whizzie notice
 */
if ( ! function_exists( 'skincare_shop_dismiss_whizzie_notice' ) ) {
    function skincare_shop_dismiss_whizzie_notice() {

        update_user_meta(
            get_current_user_id(),
            'skincare_shop_whizzie_dismissed',
            true
        );

        wp_die();
    }
}
add_action(
    'wp_ajax_skincare_shop_dismiss_whizzie_notice',
    'skincare_shop_dismiss_whizzie_notice'
);

/**
 * Check if Whizzie notice is dismissed
 */
if ( ! function_exists( 'skincare_shop_is_whizzie_dismissed' ) ) {
    function skincare_shop_is_whizzie_dismissed() {

        return (bool) get_user_meta(
            get_current_user_id(),
            'skincare_shop_whizzie_dismissed',
            true
        );

    }
}

/**
 * Reset Whizzie notice when theme is activated
 */
add_action( 'after_switch_theme', function () {

    $users = get_users( array(
        'fields' => 'ID',
    ) );

    foreach ( $users as $user_id ) {
        delete_user_meta( $user_id, 'skincare_shop_whizzie_dismissed' );
    }

});

/**
 * Display the admin notice unless dismissed.
 */
function skincare_shop_dashboard_notice() {
    // Check if the notice is dismissed
    $dismissed = get_user_meta(get_current_user_id(), 'skincare_shop_dismissable_notice', true);

    // Display the notice only if not dismissed
    if (!$dismissed) {
        ?>
        <div class="updated notice notice-success is-dismissible notice-get-started-class" data-notice="get-start">
            <div class="notice-details">
                <div class="notice-content">
                    <h2><?php /* translators: %s: Theme name */
					printf( esc_html__( 'Thanks you for installing %s.', 'skincare-shop' ), '<strong>Skincare Shop</strong>' );?></h2>
                    <p><?php echo esc_html('Your journey to a powerful and stylish website begins here. Let’s get everything set up in just a few clicks!', 'skincare-shop'); ?></p>
                    <div class="notice-btns">
                        <a class="button button-primary getstart"
                           href="<?php echo esc_url(admin_url('themes.php?page=skincare-shop')); ?>"><?php esc_html_e('Getting Started', 'skincare-shop') ?></a>
                       	<a class="button button-primary premium" target="_blank" href="<?php echo esc_url(SKINCARE_SHOP_URL); ?>"><?php esc_html_e('Go To Premium', 'skincare-shop') ?></a>
						<a class="button button-primary demo" target="_blank" href="<?php echo esc_url(SKINCARE_SHOP_DEMO_URL); ?>"><?php esc_html_e('View Demo', 'skincare-shop') ?></a>
                    </div>
                </div>
                <div class="notice-img">
                    <a href="<?php echo esc_url( SKINCARE_SHOP_BUNDLE_URL ); ?>" target="_blank"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/notice.png' ); ?>"></a>
                </div>
            </div>
        </div>
        <?php
    }
}

// Hook to display the notice
add_action('admin_notices', 'skincare_shop_dashboard_notice');

/**
 * AJAX handler to dismiss the notice.
 */
function skincare_shop_dismissable_notice() {
    // Set user meta to indicate the notice is dismissed
    update_user_meta(get_current_user_id(), 'skincare_shop_dismissable_notice', true);
    die();
}

// Hook for the AJAX action
add_action('wp_ajax_skincare_shop_dismissable_notice', 'skincare_shop_dismissable_notice');

/**
 * Clear dismissed notice state when switching themes.
 */
function skincare_shop_switch_theme() {
    // Clear the dismissed notice state when switching themes
    delete_user_meta(get_current_user_id(), 'skincare_shop_dismissable_notice');
}

// Hook for switching themes
add_action('after_switch_theme', 'skincare_shop_switch_theme');