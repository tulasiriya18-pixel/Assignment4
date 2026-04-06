<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package skincare_shop
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function skincare_shop_body_classes( $classes ) {
  global $skincare_shop_post;
  
    if( !is_page_template( 'template-home.php' ) ){
        $classes[] = 'inner';
        // Adds a class of group-blog to blogs with more than 1 published author.
    }

    if ( is_multi_author() ) {
        $classes[] = 'group-blog ';
    }

    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image';
    }
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color';
    }
    

    if( skincare_shop_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() || 'product' === get_post_type() ) && ! is_active_sidebar( 'shop-sidebar' ) ){
        $classes[] = 'full-width';
    }    

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_page() ) {
        $classes[] = 'hfeed ';
    }
  
    if( is_404() ||  is_search() ){
        $classes[] = 'full-width';
    }
  
    if( ! is_active_sidebar( 'right-sidebar' ) ) {
        $classes[] = 'full-width'; 
    }

    return $classes;
}
add_filter( 'body_class', 'skincare_shop_body_classes' );

 /**
 * 
 * @link http://www.altafweb.com/2011/12/remove-specific-tag-from-php-string.html
 */
function skincare_shop_strip_single( $tag, $string ){
    $string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
    $string=preg_replace('/<\/'.$tag.'>/i', '', $string);
    return $string;
}

if ( ! function_exists( 'skincare_shop_excerpt_more' ) ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function skincare_shop_excerpt_more($more) {
  return is_admin() ? $more : ' &hellip; ';
}
endif;
add_filter( 'excerpt_more', 'skincare_shop_excerpt_more' );


if( ! function_exists( 'skincare_shop_footer_credit' ) ):
/**
 * Footer Credits
*/
function skincare_shop_footer_credit() {

    // Check if footer copyright is enabled
    $skincare_shop_show_footer_copyright = get_theme_mod( 'skincare_shop_footer_setting', true );

    if ( ! $skincare_shop_show_footer_copyright ) {
        return; 
    }

    $skincare_shop_copyright_text = get_theme_mod('skincare_shop_footer_copyright_text');

    $skincare_shop_text = '<div class="site-info"><div class="container"><span class="copyright">';
    if ($skincare_shop_copyright_text) {
        $skincare_shop_text .= wp_kses_post($skincare_shop_copyright_text); 
    } else {
        $skincare_shop_text .= esc_html__('&copy; ', 'skincare-shop') . date_i18n(esc_html__('Y', 'skincare-shop')); 
        $skincare_shop_text .= ' <a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a>' . esc_html__('. All Rights Reserved.', 'skincare-shop');
    }
    $skincare_shop_text .= '</span>';
    $skincare_shop_text .= '<span class="by"> <a href="' . esc_url('https://www.themeignite.com/products/skincare-shop') . '" rel="nofollow" target="_blank">' . SKINCARE_SHOP_THEME_NAME . '</a>' . esc_html__(' By ', 'skincare-shop') . '<a href="' . esc_url('https://themeignite.com/') . '" rel="nofollow" target="_blank">' . esc_html__('Themeignite', 'skincare-shop') . '</a>.';
    /* translators: %s: link to WordPress.org */
    $skincare_shop_text .= sprintf(esc_html__(' Powered By %s', 'skincare-shop'), '<a href="' . esc_url(__('https://wordpress.org/', 'skincare-shop')) . '" target="_blank">WordPress</a>.');
    if (function_exists('the_privacy_policy_link')) {
        $skincare_shop_text .= get_the_privacy_policy_link();
    }
    $skincare_shop_text .= '</span></div></div>';
    echo apply_filters('skincare_shop_footer_text', $skincare_shop_text);
}
add_action('skincare_shop_footer', 'skincare_shop_footer_credit');
endif;

/**
 * Is Woocommerce activated
*/
if ( ! function_exists( 'skincare_shop_woocommerce_activated' ) ) {
  function skincare_shop_woocommerce_activated() {
    if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
  }
}

if( ! function_exists( 'skincare_shop_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function skincare_shop_change_comment_form_default_fields( $fields ){    
    // get the current commenter if available
    $skincare_shop_commenter = wp_get_current_commenter();
 
    // core functionality
    $req      = get_option( 'require_name_email' );
    $skincare_shop_aria_req = ( $req ? " aria-required='true'" : '' );
    $skincare_shop_required = ( $req ? " required" : '' );
    $skincare_shop_author   = ( $req ? __( 'Name*', 'skincare-shop' ) : __( 'Name', 'skincare-shop' ) );
    $skincare_shop_email    = ( $req ? __( 'Email*', 'skincare-shop' ) : __( 'Email', 'skincare-shop' ) );
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( 'Name', 'skincare-shop' ) . '<span class="required">*</span></label><input id="author" name="author" placeholder="' . esc_attr( $skincare_shop_author ) . '" type="text" value="' . esc_attr( $skincare_shop_commenter['comment_author'] ) . '" size="30"' . $skincare_shop_aria_req . $skincare_shop_required . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( 'Email', 'skincare-shop' ) . '<span class="required">*</span></label><input id="email" name="email" placeholder="' . esc_attr( $skincare_shop_email ) . '" type="text" value="' . esc_attr(  $skincare_shop_commenter['comment_author_email'] ) . '" size="30"' . $skincare_shop_aria_req . $skincare_shop_required. ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( 'Website', 'skincare-shop' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'skincare-shop' ) . '" type="text" value="' . esc_attr( $skincare_shop_commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;    
}
endif;
add_filter( 'comment_form_default_fields', 'skincare_shop_change_comment_form_default_fields' );

if( ! function_exists( 'skincare_shop_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function skincare_shop_change_comment_form_defaults( $defaults ){    
    $defaults['comment_field'] = '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . esc_html__( 'Comment', 'skincare-shop' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'skincare-shop' ) . '" cols="45" rows="8" aria-required="true" required></textarea></p>';
    
    return $defaults;    
}
endif;
add_filter( 'comment_form_defaults', 'skincare_shop_change_comment_form_defaults' );

if( ! function_exists( 'skincare_shop_escape_text_tags' ) ) :
/**
 * Remove new line tags from string
 *
 * @param $text
 * @return string
 */
function skincare_shop_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}
endif;

if( ! function_exists( 'wp_body_open' ) ) :
/**
 * Fire the wp_body_open action.
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
*/
function wp_body_open() {
    /**
     * Triggered after the opening <body> tag.
    */
    do_action( 'wp_body_open' );
}
endif;

if ( ! function_exists( 'skincare_shop_get_fallback_svg' ) ) :    
/**
 * Get Fallback SVG
*/
function skincare_shop_get_fallback_svg( $skincare_shop_post_thumbnail ) {
    if( ! $skincare_shop_post_thumbnail ){
        return;
    }
    
    $skincare_shop_image_size = skincare_shop_get_image_sizes( $skincare_shop_post_thumbnail );
     
    if( $skincare_shop_image_size ){ ?>
        <div class="svg-holder">
             <svg class="fallback-svg" viewBox="0 0 <?php echo esc_attr( $skincare_shop_image_size['width'] ); ?> <?php echo esc_attr( $skincare_shop_image_size['height'] ); ?>" preserveAspectRatio="none">
                    <rect width="<?php echo esc_attr( $skincare_shop_image_size['width'] ); ?>" height="<?php echo esc_attr( $skincare_shop_image_size['height'] ); ?>" style="fill:#dedddd;"></rect>
            </svg>
        </div>
        <?php
    }
}
endif;

function skincare_shop_enqueue_google_fonts() {

    require get_template_directory() . '/inc/wptt-webfont-loader.php';

    wp_enqueue_style(
        'google-fonts-inter',
        skincare_shop_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap' ),
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'google-fonts-jocky-one',
        skincare_shop_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Jockey+One&display=swap' ),
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'google-fonts-kalam',
        skincare_shop_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Kalam:wght@300;400;700&display=swap' ),
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'google-fonts-poppins',
        skincare_shop_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap' ),
        array(),
        '1.0'
    );
}
add_action( 'wp_enqueue_scripts', 'skincare_shop_enqueue_google_fonts' );


if( ! function_exists( 'skincare_shop_site_branding' ) ) :
/**
 * Site Branding
*/
function skincare_shop_site_branding(){
    $skincare_shop_logo_site_title = get_theme_mod( 'header_site_title', true );
    $skincare_shop_tagline = get_theme_mod( 'header_tagline', false );
    $skincare_shop_logo_width = get_theme_mod('logo_width', 100); // Retrieve the logo width setting

    ?>
    <div class="site-branding" style="max-width: <?php echo esc_attr(get_theme_mod('logo_width', '-1'))?>px;">
        <?php 
        // Check if custom logo is set and display it
        if (function_exists('has_custom_logo') && has_custom_logo()) {
            the_custom_logo();
        }
        if ($skincare_shop_logo_site_title):
             if (is_front_page()): ?>
            <h1 class="site-title" style="font-size: <?php echo esc_attr(get_theme_mod('skincare_shop_site_title_size', '20')); ?>px;">
            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
          </h1>
            <?php else: ?>
                <p class="site-title" itemprop="name">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                </p>
            <?php endif; ?>
        <?php endif; 
    
        if ($skincare_shop_tagline) :
            $skincare_shop_description = get_bloginfo('description', 'display');
            if ($skincare_shop_description || is_customize_preview()) :
        ?>
                <p class="site-description" itemprop="description"><?php echo $skincare_shop_description; ?></p>
            <?php endif;
        endif;
        ?>
    </div>
    <?php
}
endif;
if( ! function_exists( 'skincare_shop_navigation' ) ) :
    /**
     * Site Navigation
    */
    function skincare_shop_navigation(){
        ?>
        <nav class="main-navigation" id="site-navigation" role="navigation">
            <?php 
            wp_nav_menu( array( 
                'theme_location' => 'primary', 
                'menu_id' => 'primary-menu' 
            ) ); 
            ?>
        </nav>
        <?php
    }
endif;

if( ! function_exists( 'skincare_shop_header' ) ) :
    /**
     * Header Start
    */
    function skincare_shop_header(){
        $skincare_shop_header_image = get_header_image();
        $skincare_shop_sticky_header = get_theme_mod('skincare_shop_sticky_header');
        $skincare_shop_social_icon = get_theme_mod( 'skincare_shop_social_icon_setting', false);
        $skincare_shop_header_setting     = get_theme_mod( 'skincare_shop_header_setting', false );
        ?>
        <div id="page-site-header" class="main-header" data-sticky="<?php echo esc_attr( $skincare_shop_sticky_header ); ?>">
            <header id="masthead" class="site-header header-inner" role="banner">
                <div class="theme-menu head_bg">
                    <div class="container">
                        <div class="row menu-bg" <?php echo $skincare_shop_header_image != '' ? 'style="background-image: url(' . esc_url( $skincare_shop_header_image ) . '); background-repeat: no-repeat; background-size: cover"': ""; ?>>
                            <div class="col-xl-2 col-lg-3 col-md-5 col-md-left align-self-center">
                                <?php skincare_shop_site_branding(); ?>
                            </div>
                            <div class="col-xl-7 col-lg-6 col-md-1 align-self-center">
                                <?php skincare_shop_navigation(); ?> 
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-md-right align-self-center head-desc">
                                <?php if (class_exists('woocommerce')) { ?>
                                    <span class="user-btn">
                                        <?php if (is_user_logged_in()) { ?>
                                            <a class="account-btn" href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" title="<?php esc_attr_e('My Account','skincare-shop'); ?>">
                                                <i class="fa-solid fa-user"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a class="account-btn" href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" title="<?php esc_attr_e('My Account','skincare-shop'); ?>"></a>
                                        <?php } ?>
                                    </span>
                                <?php } ?>
                                <?php if (class_exists('woocommerce')) { ?>
                                    <span class="cart-count">
                                        <a class="cart-customlocation" href="<?php if (function_exists('wc_get_cart_url')) { echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e('View Shopping Cart', 'skincare-shop'); ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span class="cart-item-box"><?php echo esc_html(wp_kses_data(WC()->cart->get_cart_contents_count())); ?></span>
                                        </a>
                                    </span>
                                <?php } ?>
                                <?php if ( get_theme_mod( 'skincare_shop_show_hide_toggle', false ) ) : ?>
                                <span class="offcanvas-div d-flex">
                                    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#demo">
                                        <i class="fas fa-bars"></i>
                                    </button>
                                    <div class="offcanvas offcanvas-end" id="demo">
                                        <div class="offcanvas-header"> 
                                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                                        </div>
                                        <div id="secondary" class="offcanvas-body">
                                            <?php dynamic_sidebar( 'header-sidebar' ); ?>
                                        </div>
                                    </div>
                                </span>
                                <?php endif; ?>
                                <span class="header-info">
                                    <?php if ( get_theme_mod( 'skincare_shop_show_hide_search', false ) ) : ?>
                                        <div class="search-info">
                                            <div class="search-body">
                                                <button type="button" class="search-show">
                                                    <i class="<?php echo esc_attr( get_theme_mod( 'skincare_shop_search_icon', 'fas fa-search' ) ); ?>"></i>
                                                </button>
                                            </div>
                                            <div class="searchform-inner">
                                                <?php get_search_form(); ?>
                                                <button type="button" class="close" aria-label="<?php esc_attr_e( 'Close', 'skincare-shop' ); ?>">
                                                    <span aria-hidden="true">X</span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>                  
            </header>
        </div>
        <?php
    }
endif;
add_action( 'skincare_shop_header', 'skincare_shop_header', 20 );
