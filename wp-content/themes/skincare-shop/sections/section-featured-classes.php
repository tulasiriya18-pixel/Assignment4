<?php
/**
 * Trending Products Section
 * 
 * @package skincare_shop
 */

$skincare_shop_classes = get_theme_mod('skincare_shop_classes_setting', false);
$skincare_shop_number_of_featured_mission_items = get_theme_mod('skincare_shop_number_of_featured_mission_items', 0);
$skincare_shop_service_title = get_theme_mod('skincare_shop_service_title');
?>

<?php if ($skincare_shop_classes) : ?>
    <div class="our-classes">
        <div class="container">
            <div class="side-border">
                <?php if ($skincare_shop_service_title) : ?>
                    <h3><?php echo esc_html($skincare_shop_service_title); ?></h3>
                <?php endif; ?>
            </div>

            <?php if (class_exists('woocommerce')) : ?>
                <div class="tabs">
                    <ul class="tabs-nav">
                        <?php 
                        for ($i = 1; $i <= $skincare_shop_number_of_featured_mission_items; $i++) :
                            $skincare_shop_tab_title = get_theme_mod('skincare_shop_featured_mission_section_tab_' . $i);
                            if ($skincare_shop_tab_title) : ?>
                                <li class="tabs-nav-box">
                                    <a href="#tab-<?php echo esc_attr($i); ?>"><?php echo esc_html($skincare_shop_tab_title); ?></a>
                                </li>
                            <?php endif;
                        endfor; ?>
                    </ul>

                    <div class="tabs-stage">
                        <?php 
                        for ($i = 1; $i <= $skincare_shop_number_of_featured_mission_items; $i++) :
                            $skincare_shop_category_name = get_theme_mod('skincare_shop_trending_post_slider_args_' . $i);

                            if ($skincare_shop_category_name) :
                                $skincare_shop_args = array(
                                    'post_type' => 'product',
                                    'ignore_sticky_posts' => true,
                                    'posts_per_page' => 4,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'product_cat',
                                            'field'    => 'slug',
                                            'terms'    => $skincare_shop_category_name,
                                        ),
                                    ),
                                );

                                $loop = new WP_Query($skincare_shop_args);
                                ?>
                                <div id="tab-<?php echo esc_attr($i); ?>" class="featured-mission-box">
                                    <div class="container">
                                        <div class="row m-0">
                                            <?php if ($loop->have_posts()) :
                                                while ($loop->have_posts()) : $loop->the_post();
                                                    global $product;
                                                    if (!$product || !is_a($product, 'WC_Product')) {
                                                        $product = wc_get_product(get_the_ID());
                                                    }

                                                    $skincare_shop_regular_price = (float) $product->get_regular_price();
                                                    $skincare_shop_sale_price    = (float) $product->get_sale_price();
                                                    $skincare_shop_discount_percentage = 0;

                                                    if ($skincare_shop_regular_price > 0 && $skincare_shop_sale_price > 0) {
                                                        $skincare_shop_discount_percentage = round((($skincare_shop_regular_price - $skincare_shop_sale_price) / $skincare_shop_regular_price) * 100);
                                                    }
                                                    ?>
                                                    <div class="col-xl-3 col-lg-4 col-md-6 my-2 wow zoomInUp" data-wow-duration="2s">
                                                        <div class="box">
                                                            <div class="product-image-wrapper">
                                                                <?php if (has_post_thumbnail()) : ?>
                                                                    <?php the_post_thumbnail('medium'); ?>
                                                                <?php else : ?>
                                                                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/default-header.png'); ?>" alt="<?php esc_attr_e('Default', 'skincare-shop'); ?>">
                                                                <?php endif; ?>

                                                                <a class="buy-now-btn" href="<?php the_permalink(); ?>">
                                                                    <?php esc_html_e('BUY NOW', 'skincare-shop'); ?>
                                                                </a>

                                                                <?php if ($skincare_shop_discount_percentage > 0) : ?>
                                                                    <span class="discount-tag">-<?php echo esc_html($skincare_shop_discount_percentage); ?>%</span>
                                                                <?php endif; ?>

                                                                <?php if (shortcode_exists('woosw')) { ?>
                                                                    <div class="wishlist-main">
                                                                        <?php echo do_shortcode('[woosw]'); ?>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>

                                                            <div class="product-details text-center">
                                                                <h3 class="product-title">
                                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                                </h3>

                                                                <?php if (wc_review_ratings_enabled()) : ?>
                                                                    <div class="rating">
                                                                        <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <div class="price">
                                                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                                                </div>

                                                                <div class="cart-btn">
                                                                    <?php woocommerce_template_loop_add_to_cart(); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile;
                                            else : ?>
                                                <p class="no-products"><?php esc_html_e('No products found.', 'skincare-shop'); ?></p>
                                            <?php endif;
                                            wp_reset_postdata(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;
                        endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
