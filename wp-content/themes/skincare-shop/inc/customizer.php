<?php
/**
 * Skincare Shop Theme Customizer.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package skincare_shop
 */

if( ! function_exists( 'skincare_shop_customize_register' ) ):  
/**
 * Add postMessage support for site title and description for the Theme Customizer.F
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function skincare_shop_customize_register( $wp_customize ) {
    require get_parent_theme_file_path('/inc/controls/changeable-icon.php');

    require get_parent_theme_file_path('/inc/controls/sortable-control.php');
    
    //Register the sortable control type.
    $wp_customize->register_control_type( 'Skincare_Shop_Control_Sortable' ); 
    

    if ( version_compare( get_bloginfo('version'),'4.9', '>=') ) {
        $wp_customize->get_section( 'static_front_page' )->title = __( 'Static Front Page', 'skincare-shop' );
    }
	
    /* Option list of all post */	
    $skincare_shop_options_posts = array();
    $skincare_shop_options_posts_obj = get_posts('posts_per_page=-1');
    $skincare_shop_options_posts[''] = esc_html__( 'Choose Post', 'skincare-shop' );
    foreach ( $skincare_shop_options_posts_obj as $skincare_shop_posts ) {
    	$skincare_shop_options_posts[$skincare_shop_posts->ID] = $skincare_shop_posts->post_title;
    }
    
    /* Option list of all categories */
    $skincare_shop_args = array(
	   'type'                     => 'post',
	   'orderby'                  => 'name',
	   'order'                    => 'ASC',
	   'hide_empty'               => 1,
	   'hierarchical'             => 1,
	   'taxonomy'                 => 'category'
    ); 
    $skincare_shop_option_categories = array();
    $skincare_shop_category_lists = get_categories( $skincare_shop_args );
    $skincare_shop_option_categories[''] = esc_html__( 'Choose Category', 'skincare-shop' );
    foreach( $skincare_shop_category_lists as $skincare_shop_category ){
        $skincare_shop_option_categories[$skincare_shop_category->term_id] = $skincare_shop_category->name;
    }
    
    /** Default Settings */    
    $wp_customize->add_panel( 
        'wp_default_panel',
         array(
            'priority' => 10,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => esc_html__( 'Default Settings', 'skincare-shop' ),
            'description' => esc_html__( 'Default section provided by wordpress customizer.', 'skincare-shop' ),
        ) 
    );
    
    $wp_customize->get_section( 'title_tagline' )->panel                  = 'wp_default_panel';
    $wp_customize->get_section( 'colors' )->panel                         = 'wp_default_panel';
    $wp_customize->get_section( 'header_image' )->panel                   = 'wp_default_panel';
    $wp_customize->get_section( 'background_image' )->panel               = 'wp_default_panel';
    $wp_customize->get_section( 'static_front_page' )->panel              = 'wp_default_panel';
    
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
    
    /** Default Settings Ends */
    
    /** Site Title control */
    $wp_customize->add_setting( 
        'header_site_title', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'header_site_title',
        array(
            'label'       => __( 'Show / Hide Site Title', 'skincare-shop' ),
            'section'     => 'title_tagline',
            'type'        => 'checkbox',
        )
    );

    /** Tagline control */
    $wp_customize->add_setting( 
        'header_tagline', 
        array(
            'default'           => false,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'header_tagline',
        array(
            'label'       => __( 'Show / Hide Tagline', 'skincare-shop' ),
            'section'     => 'title_tagline',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting('logo_width', array(
        'sanitize_callback' => 'absint', 
    ));

    // Add a control for logo width
    $wp_customize->add_control('logo_width', array(
        'label' => __('Logo Width', 'skincare-shop'),
        'section' => 'title_tagline',
        'type' => 'number',
        'input_attrs' => array(
            'min' => '50', 
            'max' => '500', 
            'step' => '5', 
    ),
        'default' => '100', 
    ));

    $wp_customize->add_setting( 'skincare_shop_site_title_size', array(
        'default'           => 20, // Default font size in pixels
        'sanitize_callback' => 'absint', // Sanitize the input as a positive integer
    ) );

    // Add control for site title size
    $wp_customize->add_control( 'skincare_shop_site_title_size', array(
        'type'        => 'number',
        'section'     => 'title_tagline', // You can change this section to your preferred section
        'label'       => __( 'Site Title Font Size (px)', 'skincare-shop' ),
        'input_attrs' => array(
            'min'  => 10,
            'max'  => 100,
            'step' => 1,
        ),
    ) );

    /** Post & Pages Settings */
    $wp_customize->add_panel( 
        'skincare_shop_post_settings',
         array(
            'priority' => 11,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'Post & Pages Settings', 'skincare-shop' ),
            'description' => esc_html__( 'Customize Post & Pages Settings', 'skincare-shop' ),
        ) 
    );

    /** Post Layouts */
    
    $wp_customize->add_section(
        'skincare_shop_post_layout_section',
        array(
            'title' => esc_html__( 'Post Layout Settings', 'skincare-shop' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_post_settings',
        )
    );

    $wp_customize->add_setting('skincare_shop_post_layout_setting', array(
        'default'           => 'right-sidebar',
        'sanitize_callback' => 'skincare_shop_sanitize_post_layout',
    ));

    $wp_customize->add_control('skincare_shop_post_layout_setting', array(
        'label'    => __('Post Column Settings', 'skincare-shop'),
        'section'  => 'skincare_shop_post_layout_section',
        'settings' => 'skincare_shop_post_layout_setting',
        'type'     => 'select',
        'choices'  => array(        
            'right-sidebar'   => __('Right Sidebar', 'skincare-shop'),
            'left-sidebar'   => __('Left Sidebar', 'skincare-shop'),
            'one-column'   => __('One Column', 'skincare-shop'),
            'three-column'   => __('Three Columns', 'skincare-shop'),
            'four-column'   => __('Four Columns', 'skincare-shop'),
            'grid-layout'   => __('Grid Layout', 'skincare-shop')
        ),
    ));

     /** Post Layouts Ends */
     
    /** Post Settings */
    $wp_customize->add_section(
        'skincare_shop_post_settings',
        array(
            'title' => esc_html__( 'Post Settings', 'skincare-shop' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_post_settings',
        )
    );

    /** Post Heading control */
    $wp_customize->add_setting( 
        'skincare_shop_post_heading_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_post_heading_setting',
        array(
            'label'       => __( 'Show / Hide Post Heading', 'skincare-shop' ),
            'section'     => 'skincare_shop_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Meta control */
    $wp_customize->add_setting( 
        'skincare_shop_post_meta_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_post_meta_setting',
        array(
            'label'       => __( 'Show / Hide Post Meta', 'skincare-shop' ),
            'section'     => 'skincare_shop_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Image control */
    $wp_customize->add_setting( 
        'skincare_shop_post_image_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_post_image_setting',
        array(
            'label'       => __( 'Show / Hide Post Image', 'skincare-shop' ),
            'section'     => 'skincare_shop_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Post Content control */
    $wp_customize->add_setting( 
        'skincare_shop_post_content_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_post_content_setting',
        array(
            'label'       => __( 'Show / Hide Post Content', 'skincare-shop' ),
            'section'     => 'skincare_shop_post_settings',
            'type'        => 'checkbox',
        )
    );
    /** Post ReadMore control */
     $wp_customize->add_setting( 'skincare_shop_read_more_setting', array(
        'default'           => true,
        'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'skincare_shop_read_more_setting', array(
        'type'        => 'checkbox',
        'section'     => 'skincare_shop_post_settings', 
        'label'       => __( 'Display Read More Button', 'skincare-shop' ),
    ) );

    $wp_customize->add_setting('skincare_shop_blog_meta_order', array(
        'default' => array('heading', 'author', 'featured-image', 'content','button'),
        'sanitize_callback' => 'skincare_shop_sanitize_sortable',
    ));
    $wp_customize->add_control(new Skincare_Shop_Control_Sortable($wp_customize, 'skincare_shop_blog_meta_order', array(
        'label' => esc_html__('Post Meta Ordering', 'skincare-shop'),
        'description' => __('Drag & drop post items to rearrange the ordering ( this control will not function by post format )', 'skincare-shop') ,
        'section' => 'skincare_shop_post_settings',
        'choices' => array(
            'heading' => __('heading', 'skincare-shop') ,
            'author' => __('author', 'skincare-shop') ,
            'featured-image' => __('featured-image', 'skincare-shop') ,
            'content' => __('content', 'skincare-shop') ,
            'button' => __('button', 'skincare-shop') ,
        ) ,
    )));

    /** Post Settings Ends */

     /** Single Post Settings */
    $wp_customize->add_section(
        'skincare_shop_single_post_settings',
        array(
            'title' => esc_html__( 'Single Post Settings', 'skincare-shop' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_post_settings',
        )
    );

    /** Single Post Meta control */
    $wp_customize->add_setting( 
        'skincare_shop_single_post_meta_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_single_post_meta_setting',
        array(
            'label'       => __( 'Show / Hide Single Post Meta', 'skincare-shop' ),
            'section'     => 'skincare_shop_single_post_settings',
            'type'        => 'checkbox',
        )
    );

    /** Single Post Content control */
    $wp_customize->add_setting( 
        'skincare_shop_single_post_content_setting', 
        array(
            'default'           => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_single_post_content_setting',
        array(
            'label'       => __( 'Show / Hide Single Post Content', 'skincare-shop' ),
            'section'     => 'skincare_shop_single_post_settings',
            'type'        => 'checkbox',
        )
    );

    //Global Color
    $wp_customize->add_section(
        'skincare_shop_global_color',
        array(
            'title' => esc_html__( 'Global Color Settings', 'skincare-shop' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_general_settings',
        )
    );

    $wp_customize->add_setting('skincare_shop_primary_color', array(
        'default'           => '#1e1d1c',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'skincare_shop_primary_color', array(
        'label'    => __('Theme Primary Color', 'skincare-shop'),
        'section'  => 'skincare_shop_global_color',
        'settings' => 'skincare_shop_primary_color',
    )));    

    $wp_customize->add_setting('skincare_shop_second_color', array(
        'default'           => '#875228',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'skincare_shop_second_color', array(
        'label'    => __('Theme Secondary Color', 'skincare-shop'),
        'section'  => 'skincare_shop_global_color',
        'settings' => 'skincare_shop_second_color',
    )));

    $wp_customize->add_setting('skincare_shop_third_color', array(
        'default'           => '#f6e4d5',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'skincare_shop_third_color', array(
        'label'    => __('Theme Tertiary Color', 'skincare-shop'),
        'section'  => 'skincare_shop_global_color',
        'settings' => 'skincare_shop_third_color',
    )));

    /** Single Post Settings Ends */

         // Typography Settings Section
    $wp_customize->add_section('skincare_shop_typography_settings', array(
        'title'      => esc_html__('Typography Settings', 'skincare-shop'),
        'priority'   => 30,
        'capability' => 'edit_theme_options',
        'panel' => 'skincare_shop_general_settings',
    ));

    // Array of fonts to choose from
    $font_choices = array(
        ''               => __('Select', 'skincare-shop'),
        'Arial'          => 'Arial, sans-serif',
        'Verdana'        => 'Verdana, sans-serif',
        'Helvetica'      => 'Helvetica, sans-serif',
        'Times New Roman'=> '"Times New Roman", serif',
        'Georgia'        => 'Georgia, serif',
        'Courier New'    => '"Courier New", monospace',
        'Trebuchet MS'   => '"Trebuchet MS", sans-serif',
        'Tahoma'         => 'Tahoma, sans-serif',
        'Palatino'       => '"Palatino Linotype", serif',
        'Garamond'       => 'Garamond, serif',
        'Impact'         => 'Impact, sans-serif',
        'Comic Sans MS'  => '"Comic Sans MS", cursive, sans-serif',
        'Lucida Sans'    => '"Lucida Sans Unicode", sans-serif',
        'Arial Black'    => '"Arial Black", sans-serif',
        'Gill Sans'      => '"Gill Sans", sans-serif',
        'Segoe UI'       => '"Segoe UI", sans-serif',
        'Open Sans'      => '"Open Sans", sans-serif',
        'Outfit'         => 'Outfit, sans-serif',
        'Lato'           => 'Lato, sans-serif',
        'Montserrat'     => 'Montserrat, sans-serif',
        'Libre Baskerville' => 'Libre Baskerville',
        'Inter'     => 'Inter, sans-serif',
        'Jockey One'     => 'Jockey One, sans-serif',
        'kalam'     => 'kalam, cursive',
    );

    // Heading Font Setting
    $wp_customize->add_setting('skincare_shop_heading_font_family', array(
        'default'           => '',
        'sanitize_callback' => 'skincare_shop_sanitize_choicess',
    ));
    $wp_customize->add_control('skincare_shop_heading_font_family', array(
        'type'    => 'select',
        'choices' => $font_choices,
        'label'   => __('Select Font for Heading', 'skincare-shop'),
        'section' => 'skincare_shop_typography_settings',
    ));

    // Body Font Setting
    $wp_customize->add_setting('skincare_shop_body_font_family', array(
        'default'           => '',
        'sanitize_callback' => 'skincare_shop_sanitize_choicess',
    ));
    $wp_customize->add_control('skincare_shop_body_font_family', array(
        'type'    => 'select',
        'choices' => $font_choices,
        'label'   => __('Select Font for Body', 'skincare-shop'),
        'section' => 'skincare_shop_typography_settings',
    ));

    /** Typography Settings Section End */

    /** General Settings */
    $wp_customize->add_panel( 
        'skincare_shop_general_settings',
         array(
            'priority' => 11,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'General Settings', 'skincare-shop' ),
            'description' => esc_html__( 'Customize General Settings', 'skincare-shop' ),
        ) 
    );

    /** General Settings */
    $wp_customize->add_section(
        'skincare_shop_general_settings',
        array(
            'title' => esc_html__( 'Loader Settings', 'skincare-shop' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_general_settings',
        )
    );

    /** Preloader control */
    $wp_customize->add_setting( 
        'skincare_shop_header_preloader', 
        array(
            'default' => false,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_header_preloader',
        array(
            'label'       => __( 'Show Preloader', 'skincare-shop' ),
            'section'     => 'skincare_shop_general_settings',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting('skincare_shop_loader_layout_setting', array(
        'default' => 'load',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Add control for loader layout
    $wp_customize->add_control('skincare_shop_loader_layout_control', array(
        'label' => __('Preloader Layout', 'skincare-shop'),
        'section' => 'skincare_shop_general_settings',
        'settings' => 'skincare_shop_loader_layout_setting',
        'type' => 'select',
        'choices' => array(
            'load' => __('Preloader 1', 'skincare-shop'),
            'load-one' => __('Preloader 2', 'skincare-shop'),
            'ctn-preloader' => __('Preloader 3', 'skincare-shop'),
        ),
    ));

    /** Header Section Settings */
    $wp_customize->add_section(
        'skincare_shop_header_section_settings',
        array(
            'title' => esc_html__( 'Header Section Settings', 'skincare-shop' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_home_page_settings',
        )
    );

    /** Sticky Header control */
    $wp_customize->add_setting( 
        'skincare_shop_sticky_header', 
        array(
            'default' => false,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_sticky_header',
        array(
            'label'       => __( 'Show Sticky Header', 'skincare-shop' ),
            'section'     => 'skincare_shop_header_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Add Setting for Menu Font Weight
    $wp_customize->add_setting( 'skincare_shop_menu_font_weight', array(
        'default'           => '600',
        'sanitize_callback' => 'skincare_shop_sanitize_font_weight',
    ) );

    // Add Control for Menu Font Weight
    $wp_customize->add_control( 'skincare_shop_menu_font_weight', array(
        'label'    => __( 'Menu Font Weight', 'skincare-shop' ),
        'section'  => 'skincare_shop_header_section_settings',
        'type'     => 'select',
        'choices'  => array(
            '100' => __( '100 - Thin', 'skincare-shop' ),
            '200' => __( '200 - Extra Light', 'skincare-shop' ),
            '300' => __( '300 - Light', 'skincare-shop' ),
            '400' => __( '400 - Normal', 'skincare-shop' ),
            '500' => __( '500 - Medium', 'skincare-shop' ),
            '600' => __( '600 - Semi Bold', 'skincare-shop' ),
            '700' => __( '700 - Bold', 'skincare-shop' ),
            '800' => __( '800 - Extra Bold', 'skincare-shop' ),
            '900' => __( '900 - Black', 'skincare-shop' ),
        ),
    ) );

    // Add Setting for Menu Text Transform
    $wp_customize->add_setting( 'skincare_shop_menu_text_transform', array(
        'default'           => 'capitalize',
        'sanitize_callback' => 'skincare_shop_sanitize_text_transform',
    ) );

    // Add Control for Menu Text Transform
    $wp_customize->add_control( 'skincare_shop_menu_text_transform', array(
        'label'    => __( 'Menu Text Transform', 'skincare-shop' ),
        'section'  => 'skincare_shop_header_section_settings',
        'type'     => 'select',
        'choices'  => array(
            'none'       => __( 'None', 'skincare-shop' ),
            'capitalize' => __( 'Capitalize', 'skincare-shop' ),
            'uppercase'  => __( 'Uppercase', 'skincare-shop' ),
            'lowercase'  => __( 'Lowercase', 'skincare-shop' ),
        ),
    ) );

    // Menu Hover Style	
    $wp_customize->add_setting('skincare_shop_menus_style',array(
        'default' => '',
        'sanitize_callback' => 'skincare_shop_sanitize_choices'
	));
	$wp_customize->add_control('skincare_shop_menus_style',array(
        'type' => 'select',
		'label' => __('Menu Hover Style','skincare-shop'),
		'section' => 'skincare_shop_header_section_settings',
		'choices' => array(
         'None' => __('None','skincare-shop'),
         'Zoom In' => __('Zoom In','skincare-shop'),
      ),
	));

    $wp_customize->add_setting( 
        'skincare_shop_show_hide_search', 
        array(
            'default' => false ,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_show_hide_search',
        array(
            'label'       => __( 'Show Search Feild', 'skincare-shop' ),
            'section'     => 'skincare_shop_header_section_settings',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting( 
        'skincare_shop_show_hide_toggle', 
        array(
            'default' => false ,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_show_hide_toggle',
        array(
            'label'       => __( 'Show Toggle', 'skincare-shop' ),
            'section'     => 'skincare_shop_header_section_settings',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting('skincare_shop_search_icon',array(
        'default'   => 'fas fa-search',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new Skincare_Shop_Changeable_Icon(
        $wp_customize,'skincare_shop_search_icon',array(
        'label' => __('Search Icon','skincare-shop'),
        'transport' => 'refresh',
        'section'   => 'skincare_shop_header_section_settings',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 
        'skincare_shop_header_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'skincare_shop_header_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(SKINCARE_SHOP_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'skincare_shop_header_section_settings'
        )
    );

    /** Home Page Settings */
    $wp_customize->add_panel( 
        'skincare_shop_home_page_settings',
         array(
            'priority' => 9,
            'capability' => 'edit_theme_options',
            'title' => esc_html__( 'Home Page Settings', 'skincare-shop' ),
            'description' => esc_html__( 'Customize Home Page Settings', 'skincare-shop' ),
        ) 
    );

    /** Slider Section Settings */
    $wp_customize->add_section(
        'skincare_shop_slider_section_settings',
        array(
            'title' => esc_html__( 'Banner Section Settings', 'skincare-shop' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_home_page_settings',
        )
    );

    /** Slider Section control */
    $wp_customize->add_setting( 
        'skincare_shop_slider_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_slider_setting',
        array(
            'label'       => __( 'Show Banner', 'skincare-shop' ),
            'section'     => 'skincare_shop_slider_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Section Text
    $wp_customize->add_setting('skincare_shop_slider_text_extra', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('skincare_shop_slider_text_extra', 
        array(
        'label'       => __('Banner Extra Title', 'skincare-shop'),
        'section'     => 'skincare_shop_slider_section_settings',   
        'settings'    => 'skincare_shop_slider_text_extra',
        'type'        => 'text'
        )
    );

    // Section Text
    $wp_customize->add_setting('skincare_shop_banner_title', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('skincare_shop_banner_title', 
        array(
        'label'       => __('Banner Title', 'skincare-shop'),
        'section'     => 'skincare_shop_slider_section_settings',   
        'settings'    => 'skincare_shop_banner_title',
        'type'        => 'text'
        )
    );

    // header button Url
    $wp_customize->add_setting('skincare_shop_banner_title_url', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('skincare_shop_banner_title_url', 
        array(
        'label'       => __('Banner Heading URL', 'skincare-shop'),
        'section'     => 'skincare_shop_slider_section_settings',   
        'settings'    => 'skincare_shop_banner_title_url',
        'type'        => 'url'
        )
    );


    // Section Text
    $wp_customize->add_setting('skincare_shop_one_word_heading', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('skincare_shop_one_word_heading', 
        array(
        'label'       => __('Banner One Word Heading Only', 'skincare-shop'),
        'section'     => 'skincare_shop_slider_section_settings',   
        'settings'    => 'skincare_shop_one_word_heading',
        'type'        => 'text'
        )
    );

    $wp_customize->add_setting('skincare_shop_banner_product_image',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_banner_product_image',
            array(
                'label' => __('Banner Product Image','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_banner_product_image',
            )
        )
    );

    // Section Text
    $wp_customize->add_setting('skincare_shop_banner_desc', 
        array(
        'default'           => '',
        'type'              => 'theme_mod',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('skincare_shop_banner_desc', 
        array(
        'label'       => __('Banner Content', 'skincare-shop'),
        'section'     => 'skincare_shop_slider_section_settings',   
        'settings'    => 'skincare_shop_banner_desc',
        'type'        => 'text'
        )
    );

    $wp_customize->add_setting('skincare_shop_right_image_box_3_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('skincare_shop_right_image_box_3_text', array(
        'label'    => __('Review Text', 'skincare-shop'),
        'section'  => 'skincare_shop_slider_section_settings',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('skincare_shop_slider_team_image_1',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_slider_team_image_1',
            array(
                'label' => __('Review Image 1','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_slider_team_image_1',
            )
        )
    );

    $wp_customize->add_setting('skincare_shop_slider_team_image_2',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_slider_team_image_2',
            array(
                'label' => __('Review Image 2','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_slider_team_image_2',
            )
        )
    );

    $wp_customize->add_setting('skincare_shop_slider_team_image_3',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_slider_team_image_3',
            array(
                'label' => __('Review Image 3','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_slider_team_image_3',
            )
        )
    );

    $wp_customize->add_setting('skincare_shop_slider_team_image_4',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_slider_team_image_4',
            array(
                'label' => __('Review Image 4','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_slider_team_image_4',
            )
        )
    );

    $wp_customize->add_setting('skincare_shop_slider_team_image_5',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize,'skincare_shop_slider_team_image_5',
            array(
                'label' => __('Review Image 5','skincare-shop'),
                'section' => 'skincare_shop_slider_section_settings',
                'settings' => 'skincare_shop_slider_team_image_5',
            )
        )
    );

    $wp_customize->add_setting( 
        'skincare_shop_slider_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'skincare_shop_slider_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(SKINCARE_SHOP_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'skincare_shop_slider_section_settings'
        )
    );

    /** Classes Section Settings */
   $wp_customize->add_section(
        'skincare_shop_classes_section_settings',
        array(
            'title' => esc_html__( 'Product Section Settings', 'skincare-shop' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'panel' => 'skincare_shop_home_page_settings',
        )
    );

    /** Classes Section control */
    $wp_customize->add_setting( 
        'skincare_shop_classes_setting', 
        array(
            'default' => false,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_classes_setting',
        array(
            'label'       => __( 'Show Product Section', 'skincare-shop' ),
            'section'     => 'skincare_shop_classes_section_settings',
            'type'        => 'checkbox',
        )
    );

    // Section Title
    $wp_customize->add_setting(
        'skincare_shop_service_title', 
        array(
            'default'           => '',
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',    
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control(
        'skincare_shop_service_title', 
        array(
            'label'       => __('Section Title', 'skincare-shop'),
            'section'     => 'skincare_shop_classes_section_settings',
            'settings'    => 'skincare_shop_service_title',
            'type'        => 'text'
        )
    );

    // Items
    $wp_customize->add_setting('skincare_shop_number_of_featured_mission_items', 
        array(
        'default'           => '',
        'capability'        => 'edit_theme_options',    
        'sanitize_callback' => 'skincare_shop_sanitize_number_range'
        )
    );

    $wp_customize->add_control('skincare_shop_number_of_featured_mission_items', 
        array(
        'label'       => __('Items (Max: 5)', 'skincare-shop'),
        'section'     => 'skincare_shop_classes_section_settings',   
        'settings'    => 'skincare_shop_number_of_featured_mission_items',
        'type'        => 'number',
        'input_attrs' => array(
                'min'   => 1,
                'max'   => 5,
                'step'  => 1,
            ),
        )
    );

$skincare_shop_number_of_featured_mission_items = get_theme_mod( 'skincare_shop_number_of_featured_mission_items' );

for ( $skincare_shop_item_index = 1; $skincare_shop_item_index <= $skincare_shop_number_of_featured_mission_items; $skincare_shop_item_index++ ) {

    // Section Tab
    $wp_customize->add_setting( 'skincare_shop_featured_mission_section_tab_' . $skincare_shop_item_index, array(
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'skincare_shop_featured_mission_section_tab_' . $skincare_shop_item_index, array(
        'label'       => __( 'Tab ', 'skincare-shop' ) . $skincare_shop_item_index,
        'section'     => 'skincare_shop_classes_section_settings',
        'settings'    => 'skincare_shop_featured_mission_section_tab_' . $skincare_shop_item_index,
        'type'        => 'text',
    ) );

    // Default category list
    $skincare_shop_cat_posts = array( 'select' => __( 'Select', 'skincare-shop' ) );

    // Only get WooCommerce product categories if WooCommerce is active
    if ( class_exists( 'WooCommerce' ) ) {
        $categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ) );

        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            foreach ( $categories as $category ) {
                if ( isset( $category->slug, $category->name ) ) {
                    $skincare_shop_cat_posts[ $category->slug ] = $category->name;
                }
            }
        }
    }

    // Add dropdown for selecting category
    $wp_customize->add_setting(
        'skincare_shop_trending_post_slider_args_' . $skincare_shop_item_index,
        array(
            'default'           => 'select',
            'sanitize_callback' => 'skincare_shop_sanitize_choices',
        )
    );

    $wp_customize->add_control(
        'skincare_shop_trending_post_slider_args_' . $skincare_shop_item_index,
        array(
            'type'     => 'select',
            'choices'  => $skincare_shop_cat_posts,
            'label'    => __( 'Select Category to display Tab Details', 'skincare-shop' ),
            'section'  => 'skincare_shop_classes_section_settings',
        )
    );
}

    $wp_customize->add_setting( 
        'skincare_shop_classes_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'skincare_shop_classes_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(SKINCARE_SHOP_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'skincare_shop_classes_section_settings'
        )
    );

    /** Home Page Settings Ends */
    
    /** Footer Section */
    $wp_customize->add_section(
        'skincare_shop_footer_section',
        array(
            'title' => __( 'Footer Settings', 'skincare-shop' ),
            'priority' => 70,
            'panel' => 'skincare_shop_home_page_settings',
        )
    );

    /** Footer Widget Columns */
    $wp_customize->add_setting('skincare_shop_footer_widget_areas', array(
        'default'           => 4,
        'sanitize_callback' => 'skincare_shop_sanitize_choices',
    ));

    $wp_customize->add_control('skincare_shop_footer_widget_areas', array(
        'label'    => __('Footer Widget Columns', 'skincare-shop'),
        'section'  => 'skincare_shop_footer_section',
        'settings' => 'skincare_shop_footer_widget_areas',
        'type'     => 'select',
        'choices'  => array(
		   '1'     => __('One', 'skincare-shop'),
		   '2'     => __('Two', 'skincare-shop'),
		   '3'     => __('Three', 'skincare-shop'),
		   '4'     => __('Four', 'skincare-shop')
        ),
    ));

    /** Footer Copyright control */
    $wp_customize->add_setting( 
        'skincare_shop_footer_setting', 
        array(
            'default' => true,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_footer_setting',
        array(
            'label'       => __( 'Show Footer Copyright', 'skincare-shop' ),
            'section'     => 'skincare_shop_footer_section',
            'type'        => 'checkbox',
        )
    );
    
    /** Copyright Text */
    $wp_customize->add_setting(
        'skincare_shop_footer_copyright_text',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'skincare_shop_footer_copyright_text',
        array(
            'label' => __( 'Copyright Info', 'skincare-shop' ),
            'section' => 'skincare_shop_footer_section',
            'type' => 'text',
        )
    );  
    $wp_customize->add_setting('skincare_shop_footer_background_image',
        array(
        'default' => '',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
        )
    );


    $wp_customize->add_control(
         new WP_Customize_Cropped_Image_Control($wp_customize, 'skincare_shop_footer_background_image',
            array(
                'label' => esc_html__('Footer Background Image', 'skincare-shop'),
                /* translators: 1: image width in pixels, 2: image height in pixels */
                'description' => sprintf(esc_html__('Recommended Size %1$s px X %2$s px', 'skincare-shop'), 1024, 800),
                'section' => 'skincare_shop_footer_section',
                'width' => 1024,
                'height' => 800,
                'flex_width' => true,
                'flex_height' => true,
            )
        )
    );

    /** Footer Background Image Attachment */
    $wp_customize->add_setting('skincare_shop_background_attachment', array(
        'default'           => 'scroll',
        'sanitize_callback' => 'skincare_shop_sanitize_choices',
    ));

    $wp_customize->add_control('skincare_shop_background_attachment', array(
        'label'    => __('Footer Background Attachment', 'skincare-shop'),
        'section'  => 'skincare_shop_footer_section',
        'settings' => 'skincare_shop_background_attachment',
        'type'     => 'select',
        'choices'  => array(
            'fixed' => __('fixed','skincare-shop'),
            'scroll' => __('scroll','skincare-shop'),
        ),
    ));

    /* Footer Background Color*/
    $wp_customize->add_setting(
        'skincare_shop_footer_background_color',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'skincare_shop_footer_background_color',
            array(
                'label' => __('Footer Widget Area Background Color', 'skincare-shop'),
                'section' => 'skincare_shop_footer_section',
                'type' => 'color',
            )
        )
    );

   /** Scroll to top control */
    $wp_customize->add_setting( 
        'skincare_shop_footer_scroll_to_top', 
        array(
            'default' => 1,
            'sanitize_callback' => 'skincare_shop_sanitize_checkbox',
        ) 
    );

    $wp_customize->add_control(
        'skincare_shop_footer_scroll_to_top',
        array(
            'label'       => __( 'Show Scroll To Top', 'skincare-shop' ),
            'section'     => 'skincare_shop_footer_section',
            'type'        => 'checkbox',
        )
    );

     $wp_customize->add_setting('skincare_shop_scroll_icon',array(
        'default'   => 'fas fa-arrow-up',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new Skincare_Shop_Changeable_Icon(
        $wp_customize,'skincare_shop_scroll_icon',array(
        'label' => __('Scroll Top Icon','skincare-shop'),
        'transport' => 'refresh',
        'section'   => 'skincare_shop_footer_section',
        'type'      => 'icon'
    )));

    /** Scroll to top button shape */
    $wp_customize->add_setting('skincare_shop_scroll_to_top_radius', array(
        'default'           => 'curved-box',
        'sanitize_callback' => 'skincare_shop_sanitize_choices',
    ));

    $wp_customize->add_control('skincare_shop_scroll_to_top_radius', array(
        'label'    => __('Scroll Top Button Shape', 'skincare-shop'),
        'section'  => 'skincare_shop_footer_section',
        'settings' => 'skincare_shop_scroll_to_top_radius',
        'type'     => 'select',
        'choices'  => array(
            'box'        => __( 'Box', 'skincare-shop' ),
            'curved-box' => __( 'Curved Box', 'skincare-shop' ),
            'circle'     => __( 'Circle', 'skincare-shop' ),
        ),
    ));

    $wp_customize->add_setting( 
        'skincare_shop_footer_settings_upgraded_features',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
    $wp_customize->add_control(
        'skincare_shop_footer_settings_upgraded_features', 
        array(
            'type'=> 'hidden',
            'description' => "
                <div class='notice-pro-features'>
                    <div class='notice-pro-icon'>
                        <i class='fas fa-crown'></i>
                    </div>
                    <div class='notice-pro-content'>
                        <h3>Unlock Premium Features</h3>
                        <p>Enhance your website with advanced layouts, premium sections, and powerful customization tools.</p>
                    </div>
                    <div class='notice-pro-button'>
                        <a target='_blank' href='". esc_url(SKINCARE_SHOP_URL) ."' class='notice-upgrade-btn'>
                            Upgrade to Pro<i class='fas fa-rocket'></i>
                        </a>
                    </div>
                </div>
            ",
            'section' => 'skincare_shop_footer_section'
        )
    );

    // 404 PAGE SETTINGS
    $wp_customize->add_section(
        'skincare_shop_404_section',
        array(
            'title' => __( '404 Page Settings', 'skincare-shop' ),
            'priority' => 70,
            'panel' => 'skincare_shop_general_settings',
        )
    );
   
    $wp_customize->add_setting('404_page_image', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', // Sanitize as URL
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, '404_page_image', array(
        'label' => __('404 Page Image', 'skincare-shop'),
        'section' => 'skincare_shop_404_section',
        'settings' => '404_page_image',
    )));

    $wp_customize->add_setting('404_pagefirst_header', array(
        'default' => __('404', 'skincare-shop'),
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field', // Sanitize as text field
    ));

    $wp_customize->add_control('404_pagefirst_header', array(
        'type' => 'text',
        'label' => __('404 Page Heading', 'skincare-shop'),
        'section' => 'skincare_shop_404_section',
    ));

    // Setting for 404 page header
    $wp_customize->add_setting('404_page_header', array(
        'default' => __('Sorry, that page can\'t be found!', 'skincare-shop'),
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field', // Sanitize as text field
    ));

    $wp_customize->add_control('404_page_header', array(
        'type' => 'text',
        'label' => __('404 Page Content', 'skincare-shop'),
        'section' => 'skincare_shop_404_section',
    ));

}
add_action( 'customize_register', 'skincare_shop_customize_register' );
endif;

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function skincare_shop_customize_preview_js() {
    // Use minified libraries if SCRIPT_DEBUG is false
    $skincare_shop_build  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $skincare_shop_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'skincare_shop_customizer', get_template_directory_uri() . '/js' . $skincare_shop_build . '/customizer' . $skincare_shop_suffix . '.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'skincare_shop_customize_preview_js' );