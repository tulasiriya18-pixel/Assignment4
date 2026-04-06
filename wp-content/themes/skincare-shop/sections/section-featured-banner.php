<?php
/**
 * Banner Section
 * 
 * @package skincare_shop
 */
$skincare_shop_slider = get_theme_mod( 'skincare_shop_slider_setting', false );
?>

<?php if ( $skincare_shop_slider ) { ?>
  <div class="banner">
    <div class="banner-wrapper">
      <div class="banner_inner_box">
        <div class="banner_box">
            <?php if ( get_theme_mod( 'skincare_shop_slider_text_extra' ) ) : ?>
              <h6 class="slide-extra-head">
                <?php echo esc_html( get_theme_mod( 'skincare_shop_slider_text_extra' ) ); ?>
              </h6>
            <?php endif; ?>

            <?php if ( get_theme_mod( 'skincare_shop_banner_title' ) ) : ?>
              <h3 class="slide-main-head my-2">
                <a href="<?php echo esc_url( get_theme_mod( 'skincare_shop_banner_title_url') ); ?>">
                  <?php echo esc_html( get_theme_mod( 'skincare_shop_banner_title' ) ); ?>
                </a>
              </h3>
            <?php endif; ?>

            <?php if ( get_theme_mod( 'skincare_shop_banner_desc' ) ) : ?>
              <p class="mb-0 content">
                <?php echo esc_html( get_theme_mod( 'skincare_shop_banner_desc' ) ); ?>
              </p>
            <?php endif; ?>
        </div>
      <div class="product_img wow bounceInDown delay-3000" data-wow-duration="2s">
        <?php if ( get_theme_mod( 'skincare_shop_banner_product_image' ) ) { ?>
          <img src="<?php echo esc_url( get_theme_mod( 'skincare_shop_banner_product_image' ) ); ?>">
        <?php } else { ?>
          <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/product.png' ); ?>">
        <?php } ?>
      </div>
      <div class="banner_img wow bounceInLeft" data-wow-duration="2s">
          <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/slide_hand.png' ); ?>">
      </div>
      </div>
    <div class="review-box">
      <?php if ( get_theme_mod('skincare_shop_right_image_box_3_text') != '' ) { ?>
        <div class="image-box">
          <div class="image-team">
            <?php if ( get_theme_mod( 'skincare_shop_slider_team_image_1' ) ) { ?>
              <img class="team-img-small-1" src="<?php echo esc_url( get_theme_mod( 'skincare_shop_slider_team_image_1' ) ); ?>">
            <?php } ?>
            <?php if ( get_theme_mod( 'skincare_shop_slider_team_image_2' ) ) { ?>
              <img class="team-img-small-2" src="<?php echo esc_url( get_theme_mod( 'skincare_shop_slider_team_image_2' ) ); ?>">
            <?php } ?>
            <?php if ( get_theme_mod( 'skincare_shop_slider_team_image_3' ) ) { ?>
              <img class="team-img-small-3" src="<?php echo esc_url( get_theme_mod( 'skincare_shop_slider_team_image_3' ) ); ?>">
            <?php } ?>
            <?php if ( get_theme_mod( 'skincare_shop_slider_team_image_4' ) ) { ?>
              <img class="team-img-small-4" src="<?php echo esc_url( get_theme_mod( 'skincare_shop_slider_team_image_4' ) ); ?>">
            <?php } ?>
            <?php if ( get_theme_mod( 'skincare_shop_slider_team_image_5' ) ) { ?>
              <img class="team-img-small-5" src="<?php echo esc_url( get_theme_mod( 'skincare_shop_slider_team_image_5' ) ); ?>">
            <?php } ?>
          </div>
          <div class="review-content">
            <p class="mb-0 mt-2 text"><?php echo esc_html( get_theme_mod('skincare_shop_right_image_box_3_text', '') ); ?></p>
          </div>
        </div>
      <?php } ?>
    </div>
    <div class="container banner-head">
        <?php if ( get_theme_mod( 'skincare_shop_one_word_heading' ) ) : ?>
          <h1 class="slide-main-head my-2">
            <?php echo esc_html( get_theme_mod( 'skincare_shop_one_word_heading' ) ); ?>
          </h1>
        <?php endif; ?>
    </div>
  </div>
<?php } ?>