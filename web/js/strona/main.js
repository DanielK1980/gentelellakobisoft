;
(function () {

    'use strict';

    var mobileMenuOutsideClick = function () {

        $(document).click(function (e) {
            var container = $("#gtco-offcanvas, .js-gtco-nav-toggle");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $('.js-gtco-nav-toggle').addClass('gtco-nav-white');

                if ($('body').hasClass('offcanvas')) {

                    $('body').removeClass('offcanvas');
                    $('.js-gtco-nav-toggle').removeClass('active');

                }


            }
        });

    };


    var offcanvasMenu = function () {

        $('#page').prepend('<div id="gtco-offcanvas" />');
        $('#page').prepend('<a href="#" class="js-gtco-nav-toggle gtco-nav-toggle gtco-nav-white"><i></i></a>');
        var clone1 = $('.menu-1 > ul').clone();
        $('#gtco-offcanvas').append(clone1);
        var clone2 = $('.menu-2 > ul').clone();
        $('#gtco-offcanvas').append(clone2);

        $('#gtco-offcanvas .has-dropdown').addClass('offcanvas-has-dropdown');
        $('#gtco-offcanvas')
                .find('li')
                .removeClass('has-dropdown');

        // Hover dropdown menu on mobile
        $('.offcanvas-has-dropdown').mouseenter(function () {
            var $this = $(this);

            $this
                    .addClass('active')
                    .find('ul')
                    .slideDown(500, 'easeOutExpo');
        }).mouseleave(function () {

            var $this = $(this);
            $this
                    .removeClass('active')
                    .find('ul')
                    .slideUp(500, 'easeOutExpo');
        });


        $(window).resize(function () {

            if ($('body').hasClass('offcanvas')) {

                $('body').removeClass('offcanvas');
                $('.js-gtco-nav-toggle').removeClass('active');

            }
        });
    };


    var burgerMenu = function () {

        $('body').on('click', '.js-gtco-nav-toggle', function (event) {
            var $this = $(this);

            if ($('body').hasClass('overflow offcanvas')) {
                $('body').removeClass('overflow offcanvas');
            } else {
                $('body').addClass('overflow offcanvas');
            }
            $this.toggleClass('active');
            event.preventDefault();

        });
    };



    var contentWayPoint = function () {
        var i = 0;

        // $('.gtco-section').waypoint( function( direction ) {
        $('.animate-box').waypoint(function (direction) {

            if (direction === 'down' && !$(this.element).hasClass('animated-fast')) {

                i++;

                $(this.element).addClass('item-animate');
                setTimeout(function () {

                    $('body .animate-box.item-animate').each(function (k) {
                        var el = $(this);
                        setTimeout(function () {
                            var effect = el.data('animate-effect');
                            if (effect === 'fadeIn') {
                                el.addClass('animated fadeIn animated-fast');
                            } else if (effect === 'fadeInLeft') {
                                el.addClass('animated fadeInLeft animated-fast');
                            } else if (effect === 'fadeInRight') {
                                el.addClass('animated fadeInRight animated-fast');
                            } else {
                                el.addClass('animated fadeInUp animated-fast');
                            }

                            el.removeClass('item-animate');
                        }, k * 200, 'easeInOutExpo');
                    });

                }, 100);

            }

        }, {offset: '85%'});
        // }, { offset: '90%'} );
    };


    var dropdown = function () {

        $('.has-dropdown').mouseenter(function () {

            var $this = $(this);
            $this
                    .find('.dropdown')
                    .css('display', 'block')
                    .addClass('animated fadeInUpMenu animated-fast');

        }).mouseleave(function () {
            var $this = $(this);

            $this
                    .find('.dropdown')
                    .css('display', 'none')
                    .removeClass('animated fadeInUpMenu animated-fast');
        });

    };


    var owlCarousel = function () {

        var owl = $('.owl-carousel-carousel');
        owl.owlCarousel({
            items: 3,
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            smartSpeed: 800,
            autoHeight: true,
            navText: [
                "<i class='ti-arrow-left owl-direction'></i>",
                "<i class='ti-arrow-right owl-direction'></i>"
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });


        var owl = $('.owl-carousel-fullwidth');
        owl.owlCarousel({
            items: 1,
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            smartSpeed: 800,
            autoHeight: true,
            navText: [
                "<i class='ti-arrow-left owl-direction'></i>",
                "<i class='ti-arrow-right owl-direction'></i>"
            ]
        });

    };

    var tabs = function () {

        // Auto adjust height
        $('.gtco-tab-content-wrap').css('height', 0);
        var autoHeight = function () {

            setTimeout(function () {

                var tabContentWrap = $('.gtco-tab-content-wrap'),
                        tabHeight = $('.gtco-tab-nav').outerHeight(),
                        formActiveHeight = $('.tab-content.active').outerHeight(),
                        totalHeight = parseInt(tabHeight + formActiveHeight + 90);

                tabContentWrap.css('height', totalHeight);

                $(window).resize(function () {
                    var tabContentWrap = $('.gtco-tab-content-wrap'),
                            tabHeight = $('.gtco-tab-nav').outerHeight(),
                            formActiveHeight = $('.tab-content.active').outerHeight(),
                            totalHeight = parseInt(tabHeight + formActiveHeight + 90);

                    tabContentWrap.css('height', totalHeight);
                });

            }, 100);

        };

        autoHeight();


        // Click tab menu
        $('.gtco-tab-nav a').on('click', function (event) {

            var $this = $(this),
                    tab = $this.data('tab');

            $('.tab-content')
                    .addClass('animated animated-fast fadeOutDown');

            $('.tab-content')
                    .removeClass('active');

            $('.gtco-tab-nav li').removeClass('active');

            $this
                    .closest('li')
                    .addClass('active');

            $this
                    .closest('.gtco-tabs')
                    .find('.tab-content[data-tab-content="' + tab + '"]')
                    .removeClass('animated animated-fast fadeOutDown')
                    .addClass('animated animated-fast active fadeIn');


            autoHeight();
            event.preventDefault();

        });
    };


    var goToTop = function () {

        $('.js-gotop').on('click', function (event) {

            event.preventDefault();

            $('html, body').animate({
                scrollTop: $('html').offset().top
            }, 500, 'easeInOutExpo');

            return false;
        });

        $(window).scroll(function () {

            var $win = $(window);
            if ($win.scrollTop() > 200) {
                $('.js-top').addClass('active');
            } else {
                $('.js-top').removeClass('active');
            }

        });

    };


    // Loading page
    var loaderPage = function () {
        $(".gtco-loader").fadeOut("slow");
    };

    var counter = function () {
        $('.js-counter').countTo({
            formatter: function (value, options) {
                return value.toFixed(options.decimals);
            },
        });
    };

    var counterWayPoint = function () {
        if ($('#gtco-counter').length > 0) {
            $('#gtco-counter').waypoint(function (direction) {

                if (direction === 'down' && !$(this.element).hasClass('animated')) {
                    setTimeout(counter, 400);
                    $(this.element).addClass('animated');
                }
            }, {offset: '90%'});
        }
    };


    $(function () {
        mobileMenuOutsideClick();
        offcanvasMenu();
        burgerMenu();
        contentWayPoint();
        dropdown();
        owlCarousel();
        tabs();
        goToTop();
        loaderPage();
        counterWayPoint();

        var currentLocation = window.location;

        if (currentLocation.hash && currentLocation.hash != "#") {
            var hash = currentLocation.hash;
            if (hash.substring(0,1) == "#") {
                $('html, body').animate({
                    scrollTop: $("section" + currentLocation.hash).offset().top - 200
                }, 500);
            }
        }

    });

    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();
        // console.log(scroll);
        if (scroll >= 50) {

            $('nav.gtco-nav').addClass('fixed');
        } else {
            $('nav.gtco-nav').removeClass('fixed');
        }
    });

    var sections = $('div section');
    var nav = $('nav.gtco-nav');

    $(window).on('scroll', function () {
        var cur_pos = $(this).scrollTop();
        sections.each(function () {
            var top = $(this).offset().top - 200;
            var bottom = top + $(this).outerHeight() - 200;
            //    console.log("cur_pos: "+ cur_pos);
            //    console.log("top: "+ top);
            //    console.log("bottom: "+ bottom);
            if (cur_pos >= top && cur_pos <= bottom) {
                nav.find('li').removeClass('active');
                nav.find('li a').blur();
                nav.find('li.' + $(this).attr('id')).addClass('active');
            }
        });
    });

    nav.find('a').on('click', function () {
        var $el = $(this);
        var id = $el.attr('href');
        if (id != "#" && id.substring(0,1) == "#") {
            $('html, body').animate({
                scrollTop: $("section" + id).offset().top - 200
            }, 500);
        }
        return true;
    });


}());