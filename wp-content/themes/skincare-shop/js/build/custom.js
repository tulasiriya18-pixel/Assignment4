jQuery(document).ready(function ($) {

	$('.mobile-nav .toggle-button').on( 'click', function() {
		$('.mobile-nav .main-navigation').slideToggle();
	});

	$('.mobile-nav-wrap .close ').on( 'click', function() {
		$('.mobile-nav .main-navigation').slideToggle();

	});

	$('<button class="submenu-toggle"></button>').insertAfter($('.mobile-nav ul .menu-item-has-children > a'));
	$('.mobile-nav ul li .submenu-toggle').on( 'click', function() {
		$(this).next().slideToggle();
		$(this).toggleClass('open');
	});

	//accessible menu for edge
	 $("#site-navigation ul li a").on( 'focus', function() {
	   $(this).parents("li").addClass("focus");
	}).on( 'blur', function() {
	    $(this).parents("li").removeClass("focus");
	 });

	jQuery('.tabs-stage .featured-mission-box').hide();
    jQuery('.tabs-stage div:first').show();
    jQuery('.tabs-nav li:first').addClass('tab-active');

    // Change tab class and display content
    jQuery('.tabs-nav a').on('click', function(event){
        event.preventDefault();
        jQuery('.tabs-nav li').removeClass('tab-active');
        jQuery(this).parent().addClass('tab-active');
        jQuery('.tabs-stage .featured-mission-box').hide();
        jQuery(jQuery(this).attr('href')).show();
    });

    //header-search
	jQuery('.search-show').click(function(){
		jQuery('.searchform-inner').css('visibility','visible');
	});

	jQuery('.close').click(function(){
		jQuery('.searchform-inner').css('visibility','hidden');
	});

});

var btn = jQuery('#button');

jQuery(window).scroll(function() {
  if (jQuery(window).scrollTop() > 300) {
    btn.addClass('show');
  } else {
    btn.removeClass('show');
  }
});
btn.on('click', function(e) {
  e.preventDefault();
  jQuery('html, body').animate({scrollTop:0}, '300');
});

window.addEventListener('load', (event) => {
    jQuery(".preloader").delay(1000).fadeOut("slow");
});

jQuery(window).scroll(function() {
    var data_sticky = jQuery(' .main-header').attr('data-sticky');

    if (data_sticky == 1) {
      if (jQuery(this).scrollTop() > 1){  
        jQuery('.main-header').addClass("sticky-head");
      } else {
        jQuery('.main-header').removeClass("sticky-head");
      }
    }
});

function skincare_shop_preloderFunction() {
    setTimeout(function() {           
        document.getElementById("page-top").scrollIntoView();
        
        $('#ctn-preloader').addClass('loaded');  
        // Once the preloader has finished, the scroll appears 
        $('body').removeClass('no-scroll-y');

        if ($('#ctn-preloader').hasClass('loaded')) {
            // It is so that once the preloader is gone, the entire preloader section will removed
            $('#preloader').delay(1000).queue(function() {
                $(this).remove();
                
                // If you want to do something after removing preloader:
                skincare_shop_afterLoad();
                
            });
        }
    }, 3000);
}
function skincare_shop_afterLoad() {
    // After Load function body!
}