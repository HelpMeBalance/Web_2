// ==================================================
// * Project Name   :  Talking Minds – Psychotherapist Site Template
// * File           :  JS Base
// * Version        :  1.0.0
// * Last change    :  19 August 2023, Saturday
// * Author         :  Merkulove (https://1.envato.market/tf-merkulove)
// ==================================================

(function($) {
  "use strict";

  // Back To Top - Start
  // --------------------------------------------------
  $(window).scroll(function() {
    if ($(this).scrollTop() > 200) {
      $('.backtotop:hidden').stop(true, true).fadeIn();
    } else {
      $('.backtotop').stop(true, true).fadeOut();
    }
  });
  $(function() {
    $(".scroll").on('click', function() {
      $("html,body").animate({scrollTop: 0}, "slow");
      return false
    });
  });
  // Back To Top - End
  // --------------------------------------------------

  // Sticky Header - Start
  // --------------------------------------------------
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 0) {
      $('.site_header').addClass("sticky")
    } else {
      $('.site_header').removeClass("sticky")
    }
  });
  // Sticky Header - End
  // --------------------------------------------------

  // Pricing Toggle Button - Start
  // --------------------------------------------------
  $(".pricing_toggle_btn").on('click',function(){
    $(this).toggleClass("active");
    $(".pricing_item").toggleClass("active");
  })
  // Pricing Toggle Button - End
  // --------------------------------------------------

  // Dropdown - Start
  // --------------------------------------------------
  $(document).ready(function () {
    $(".dropdown").on('mouseover', function () {
      $(this).find('> .dropdown-menu').addClass('show');
    });
    $(".dropdown").on('mouseout', function () {
      $(this).find('> .dropdown-menu').removeClass('show');
    });
  });
  // Dropdown - End
  // --------------------------------------------------

  // Odometer Counter - Start
  // --------------------------------------------------
  jQuery('.odometer').appear(function (e) {
    var odo = jQuery(".odometer");
    odo.each(function () {
      var countNumber = jQuery(this).attr("data-count");
      jQuery(this).html(countNumber);
    });
  });
  // Odometer Counter - End
  // --------------------------------------------------

  // Videos & Images popup - Start
  // --------------------------------------------------
  $('.popup_video').magnificPopup({
    type: 'iframe',
    preloader: false,
    removalDelay: 160,
    mainClass: 'mfp-fade',
    fixedContentPos: false
  });

  $('.zoom-gallery').magnificPopup({
    delegate: '.popup_image',
    type: 'image',
    closeOnContentClick: false,
    closeBtnInside: false,
    mainClass: 'mfp-with-zoom mfp-img-mobile',
    gallery: {
      enabled: true
    },
    zoom: {
      enabled: true,
      duration: 300,
      opener: function(element) {
        return element.find('img');
      }
    }

  });
  // Videos & Images popup - End
  // --------------------------------------------------

  // Carousels - Start
  // --------------------------------------------------
  $('.carousel_1col').slick({
    dots: true,
    speed: 1000,
    arrows: true,
    infinite: true,
    autoplay: true,
    slidesToShow: 1,
    pauseOnHover: true,
    autoplaySpeed: 5000,
    prevArrow: ".c1c_arrow_left",
    nextArrow: ".c1c_arrow_right"
  });

  $('.carousel_2col').slick({
    dots: true,
    speed: 1000,
    arrows: true,
    infinite: true,
    autoplay: true,
    slidesToShow: 2,
    slidesToScroll: 2,
    pauseOnHover: true,
    autoplaySpeed: 5000,
    prevArrow: ".c2c_arrow_left",
    nextArrow: ".c2c_arrow_right",
    responsive: [
    {
      breakpoint: 992,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    ]
  });

  const slider = $(".certificates_carousel");
  slider.slick({
    dots: true,
    speed: 800,
    arrows: true,
    infinite: true,
    autoplay: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    pauseOnHover: true,
    autoplaySpeed: 4000,
    centerPadding: '0px',
    prevArrow: ".c3c_arrow_left",
    nextArrow: ".c3c_arrow_right",
    responsive: [
    {
      breakpoint: 576,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 992,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    }
    ]
  });
  slider.on("wheel", function (e) {
    e.preventDefault();
    if (e.originalEvent.deltaY < 0) {
      $(this).slick("slickPrev");
    } else {
      $(this).slick("slickNext");
    }
  });

  // Carousels - End
  // --------------------------------------------------

})(jQuery);
