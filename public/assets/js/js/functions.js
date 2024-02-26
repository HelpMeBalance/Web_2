(function ($) {
    "use strict";
    $(window).on('load', function(){
        $('.preloader').fadeOut(500);
    })


    $(document).ready(function () {

        /*=========Mobile menu start ============*/
        $('#mobile-nav').hcOffcanvasNav({
            position: 'right',
            height: 500,
            customToggle: '.open-menu',
            levelOpen: 'overlap',
            bodyInsert: 'append',
        });

        /*=========Mobile menu end ============*/

        /*======|| Header Menu Start here ||=======*/

        //menu has submenu plus icon
        $(".main-menu>li>.submenu").parent("li").children("a").addClass("dd-icon-down");
        $(".m-menu>li>.m-submenu").parent("li").children("a").addClass("dd-icon-down");
        $(".shop-menu>li>.shop-submenu").parent("li").children("a").addClass("dd-icon-down");
        $(".main-menu>li>.submenu .submenu").parent("li").children("a").addClass("dd-icon-right");
        $(".m-menu>li>.m-submenu .submenu").parent("li").children("a").addClass("dd-icon-right");


        //shop-widget drodown menu display
        $('.shop-widget .shop-menu li a').on('click', function (e) {
            var element = $(this).parent('li');
            if (element.hasClass('open')) {
                element.removeClass('open');
                element.find('li').removeClass('open');
                element.find('ul').slideUp(1000, "swing");
            } else {
                element.addClass('open');
                element.children('ul').slideDown(1000, "swing");
                element.siblings('li').children('ul').slideUp(1000, "swing");
                element.siblings('li').removeClass('open');
                element.siblings('li').find('li').removeClass('open');
                element.siblings('li').find('ul').slideUp(1000, "swing");
            }
        });

        // drop down menu width overflow problem fix
        $('ul').parent().hover(function () {
            var menu = $(this).find("ul");
            var menupos = $(menu).offset();

            if (menupos.left + menu.width() > $(window).width()) {
                var newpos = -$(menu).width();
                menu.css({
                    left: newpos
                });
            }
        });

        //header options in menu fixed
        var fixed_top = $(".header-bottom");
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 100) {
                fixed_top.addClass("animated fadeInDown menu-fixed");
            } else {
                fixed_top.removeClass("animated fadeInDown menu-fixed");
            }
        });

        //Js code for search box start
        $(".search").on("click", function () {
            $(".nav-search").toggleClass("open");
        });
        /*======|| Header Menu end here ||=======*/

        // counter up start
        $('.counter').counterUp({
            delay: 10,
            time: 2000
        });

        // Sponsor Section slider
        var swiper = new Swiper('.sponsor-area', {
            slidesPerView: 6,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            // Responsive breakpoints
            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 2,
                },
                // when window width is >= 576px
                575: {
                    slidesPerView: 2,
                },
                // when window width is >= 768px
                767: {
                    slidesPerView: 3,
                },
                // when window width is >= 992px
                991: {
                    slidesPerView: 4,
                },
                // when window width is >= 1200px
                1199: {
                    slidesPerView: 5,
                }
            },
            loop: true,
        });

        //Aid section slider
        var galleryThumbs = new Swiper('.gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 3,
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            breakpoints: {
                575: {
                    slidesPerView: 2,
                }
            }
        });
        var galleryThumbs = new Swiper('.gallery-top', {
            spaceBetween: 10,
            thumbs: {
                swiper: galleryThumbs
            },
        });

        // testi slider
        var swiper = new Swiper('.testi-slider', {
            slidesPerView: 3,
            spaceBetween: 0,
            speed: 1200,
            pagination: {
                el: '.testi-pagination',
                clickable: true
            },
            breakpoints: {
                991: {
                    slidesPerView: 2,
                },
                767: {
                    slidesPerView: 1,
                }
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            loop: true
        });
        // blog slider start here
        var swiper = new Swiper('.blog-slider', {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 1200,
            navigation: {
                nextEl: '.blog-slider-next',
                prevEl: '.blog-slider-prev',
            },
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            loop: true
        });

        //Shop-single Image Slider
        var galleryThumbs = new Swiper('.shop-thumb-bottom', {
            spaceBetween: 10,
            slidesPerView: 4,
            loop: true,
            freeMode: true,
            loopedSlides: 5, //looped slides should be the same
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
        });
        var galleryTop = new Swiper('.shop-thumb-top', {
            spaceBetween: 10,
            loop: true,
            loopedSlides: 4, //looped slides should be the same
            navigation: {
                nextEl: '.product-img-next',
                prevEl: '.product-img-prev',
            },
            thumbs: {
                swiper: galleryThumbs,
            },
        });

        // scroll up start here
        $(function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 300) {
                    $('.scrollToTop').css({
                        'bottom': '2%',
                        'opacity': '1',
                        'transition': 'all .5s ease'
                    });
                } else {
                    $('.scrollToTop').css({
                        'bottom': '-30%',
                        'opacity': '0',
                        'transition': 'all .5s ease'
                    })
                }
            });

            //Click event to scroll to top
            $('.scrollToTop').on('click', function () {
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                return false;
            });
        });

        // blog section post-thumb-slider
        var swiper = new Swiper('.post-thumb-slider', {
            slidesPerView: 1,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.post-thumb-slider-next',
                prevEl: '.post-thumb-slider-prev',
            },
            loop: true,
        });




        // shop cart + - start here
        var CartPlusMinus = $('.cart-plus-minus');
        $(".qtybutton").on("click", function () {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            if ($button.text() === "+") {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                if (oldValue > 0) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 1;
                }
            }
            $button.parent().find("input").val(newVal);
        });

        // product view mode change js
        $(function () {
            $('.product-view-mode').on('click', 'a', function (e) {
                e.preventDefault();
                var shopProductWrap = $('.shop-product-wrap');
                var viewMode = $(this).data('target');
                $('.product-view-mode a').removeClass('active');
                $(this).addClass('active');
                shopProductWrap.removeClass('grid list').addClass(viewMode);
            });
        });


        // product single thumb slider
        $(function () {
            var galleryThumbs = new Swiper('.pro-single-thumbs', {
                spaceBetween: 10,
                slidesPerView: 3,
                loop: true,
                freeMode: true,
                loopedSlides: 5,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
                navigation: {
                    nextEl: '.pro-single-next',
                    prevEl: '.pro-single-prev',
                },
            });
            var galleryTop = new Swiper('.pro-single-top', {
                spaceBetween: 10,
                loop: true,
                loopedSlides: 5,
                thumbs: {
                    swiper: galleryThumbs,
                },
            });
        });

        //Review Tabs
        $('ul.review-nav').on('click', 'li', function (e) {
            e.preventDefault();
            var reviewContent = $('.review-content');
            var viewRev = $(this).data('target');
            $('ul.review-nav li').removeClass('active');
            $(this).addClass('active');
            reviewContent.removeClass('review-content-show description-show').addClass(viewRev);
        });

        // wow animation
        new WOW().init();

        //mouse hover active
        $('.doctor-item').on('mouseenter', function () {
            $(this).addClass('active').parent().siblings().find('.doctor-item').removeClass('active');
        });
    });
}(jQuery));


/*===  Circular progressbar js(Doctor-single-page) ===  */
window.addEventListener('DOMContentLoaded', function () {
    const circle = new CircularProgressBar('pie');
    setInterval(() => {
        const options = {
            index: 0,
            percent: Math.floor((Math.random() * 100) + 1)
        }
        circle.animationTo(options);
    }, 2000);
});