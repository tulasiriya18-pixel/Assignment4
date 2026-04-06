<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package skincare_shop
 */
$skincare_shop_scroll_top  = get_theme_mod( 'skincare_shop_scroll_to_top', true );
$skincare_shop_footer_background = get_theme_mod('skincare_shop_footer_background_image');
$skincare_shop_footer_background_url = '';
if(!empty($skincare_shop_footer_background)){
    $skincare_shop_footer_background = absint($skincare_shop_footer_background);
    $skincare_shop_footer_background_url = wp_get_attachment_url($skincare_shop_footer_background);
}

$skincare_shop_footer_background_color = get_theme_mod('skincare_shop_footer_background_color', 'var(--primary-color)'); // New line

$skincare_shop_footer_background_style = '';
if (!empty($skincare_shop_footer_background_url)) {
    $skincare_shop_footer_background_style = ' style="background-image: url(\'' . esc_url($skincare_shop_footer_background_url) . '\'); background-repeat: no-repeat; background-size: cover;"';
} else {
    $skincare_shop_footer_background_style = ' style="background-color: ' . esc_attr($skincare_shop_footer_background_color) . ';"'; // Updated line
}
?>

</div>
</div>
</div>
</div>

<footer class="site-footer" <?php echo $skincare_shop_footer_background_style; ?>>
    <?php 
    $skincare_shop_active_areas = get_theme_mod('skincare_shop_footer_widget_areas', 4);
    if (
        is_active_sidebar('footer-1') ||
        is_active_sidebar('footer-2') ||
        is_active_sidebar('footer-3') ||
        is_active_sidebar('footer-4')
    ) : ?>
        <div class="footer-t">
            <div class="container">
                <div class="row wow bounceInUp center delay-1000" data-wow-duration="2s">
                    <?php 
                    for ($skincare_shop_i = 1; $skincare_shop_i <= $skincare_shop_active_areas; $skincare_shop_i++) {

                        if (is_active_sidebar('footer-' . $skincare_shop_i)) {

                            $skincare_shop_col = 12 / $skincare_shop_active_areas;

                            echo '<div class="col-xl-' . $skincare_shop_col . ' col-lg-' . $skincare_shop_col . ' col-md-6 col-sm-6">';
                            dynamic_sidebar('footer-' . $skincare_shop_i);
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

    <?php else : ?>

        <!-- Default Widget Content -->
        <div class="footer-t">
            <div class="container">
                <div class="row wow bounceInUp center delay-1000" data-wow-duration="2s">

                    <?php 
                    // Dynamic column width
                    $skincare_shop_col = 12 / $skincare_shop_active_areas;
                    ?>

                    <!-- Archive -->
                    <aside class="widget widget_archive col-xl-<?php echo $skincare_shop_col; ?> col-lg-<?php echo $skincare_shop_col; ?> col-md-6 col-sm-6">
                        <h2 class="widget-title"><?php esc_html_e('Archive List', 'skincare-shop'); ?></h2>
                        <ul><?php wp_get_archives('type=monthly'); ?></ul>
                    </aside>

                    <!-- Recent Posts -->
                    <aside class="widget widget_recent_posts col-xl-<?php echo $skincare_shop_col; ?> col-lg-<?php echo $skincare_shop_col; ?> col-md-6 col-sm-6">
                        <h2 class="widget-title"><?php esc_html_e('Recent Posts', 'skincare-shop'); ?></h2>
                        <ul>
                            <?php
                            $args = array('post_type' => 'post', 'posts_per_page' => 5);
                            $recent_posts = new WP_Query($args);
                            while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    </aside>

                    <!-- Categories -->
                    <aside class="widget widget_categories col-xl-<?php echo $skincare_shop_col; ?> col-lg-<?php echo $skincare_shop_col; ?> col-md-6 col-sm-6">
                        <h2 class="widget-title"><?php esc_html_e('Categories', 'skincare-shop'); ?></h2>
                        <ul><?php wp_list_categories(array('title_li' => '')); ?></ul>
                    </aside>

                    <!-- Tags -->
                    <aside class="widget widget_tags col-xl-<?php echo $skincare_shop_col; ?> col-lg-<?php echo $skincare_shop_col; ?> col-md-6 col-sm-6">
                        <h2 class="widget-title"><?php esc_html_e('Tags', 'skincare-shop'); ?></h2>
                        <div class="tag-cloud"><?php wp_tag_cloud(); ?></div>
                    </aside>

                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php do_action('skincare_shop_footer'); ?>

    <?php if ($skincare_shop_scroll_top) : ?>
        <a id="button">
            <i class="<?php echo esc_attr(get_theme_mod('skincare_shop_scroll_icon', 'fas fa-arrow-up')); ?>"></i>
        </a>
    <?php endif; ?>

</footer>
</div>
</div>

<?php wp_footer(); ?>

</body>
</html>