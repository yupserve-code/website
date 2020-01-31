/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {

    var url = window.location.href;
    $('.inner-sidebar ul li a[href="' + url + '"]').closest('li').addClass('active');


    $(".translation-links a").on('click', function () {
        //debugger
        var language = $(this).attr("data-lang");
        var url = window.location.href;
        if (language === "English") {
            url = url.slice(0, -3);
            //url = url + '/';
            //alert(url);
            window.location.replace(url);
        } else {
            //url = url.slice(0, -1);

            //alert(url);



            if ($('body').hasClass('innerpage')) {
                url = url + 'hi/';
            } else {
                url = url + 'hn/';
            }

            window.location.replace(url);
        }

    });




    //gallery Start
    $('.inner-gallery-section').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            }
        }
    });
//gallery End
//
//
///Scroll to Top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 450) {
            $('#gotoTop').fadeIn();
        } else {
            $('#gotoTop').fadeOut();
        }
    });


    $('#gotoTop').click(function () {
        $('body,html').animate({scrollTop: 0}, 400);
        return false;
    });
//Scroll to Top

//$("#year-modal").on('click',function(){
//    debugger
//   // $('.marquee marquee').start();
//    document.getElementById("trainmarquee").start();
//});
//
//var trainmarquee=0;
//$("#trainmarquee .nalco-train-cont").each(function(){
//    trainmarquee=trainmarquee+($(this).width());
//});
//
//$("#trainmarquee").width(trainmarquee);


//$("#year-filter").change(function() {
//    var year = $(this).value();
//    $('#' + 'year');
//});

    $('#year-filter').on('change', function () {
//debugger
        var year = $(this).val();
        var xyz = $('#' + year).position().top;
        var klm = $('#' + year).innerHeight();
        var debasis = xyz + klm;
        //$('#'(this).val()).addClass('visible');  
//        $('this').removeClass('active');
//        $(this).addClass('active');
        $('body, html').animate({scrollTop: debasis});
    });

    if ($("video").length) {

        var evenodd = '';
        if (typeof (Storage) !== "undefined") {

            if (sessionStorage.clickcount) {
                sessionStorage.clickcount = Number(sessionStorage.clickcount) + 1;
            } else {
                sessionStorage.clickcount = 1;
            }

            var evenodd = sessionStorage.clickcount;
        } else {
            // Sorry! No Web Storage support..
        }

        var videoHtml = '';
        if (evenodd % 2 == 0) {
            videoHtml += '<source src="https://res.cloudinary.com/prabhas/video/upload/v1540447892/NALCO/nalco-csr.webm" type="video/webm">';
            videoHtml += '<source src="https://res.cloudinary.com/prabhas/video/upload/v1540447818/NALCO/csr.mp4" type="video/mp4">';
        } else {
            videoHtml += '<source src="https://res.cloudinary.com/prabhas/video/upload/v1545836796/NALCO/new-26-12-2018.webm" type="video/webm">';
            videoHtml += '<source src="https://res.cloudinary.com/prabhas/video/upload/v1545836146/NALCO/new-26-120218.mp4" type="video/mp4">';
        }







        $("#bgvid").html(videoHtml);

//snippet.log('before:  ' + video.getBoundingClientRect().width)
        var img = new Image();
        img.src = video.getAttribute('poster');
        img.onload = function () {

            $(".loader").addClass('hideLoader');
            setTimeout(function () {
                $(".loader").fadeOut();
            }, 1500);
        }
    }
//	snippet.log('before:  '+video.getBoundingClientRect().width);
//var img = new Image();
//img.src = video.getAttribute('poster');
//img.onload = function(){snippet.log('loaded: ' + video.getBoundingClientRect().width); }




    jQuery(window).on('scroll', function () {
        (function ($) {
            stickyHeader();
        })(jQuery);
    });


    $(".tooltip-button").on("click", function () {
        $(".top-right").slideToggle(400);
    });


    $(".theme-switcher").click(function () {
        $("#theme-options").toggleClass("active")
    })


    $(".theme-switcher-left").click(function () {
        $("#theme-options-left").toggleClass("active")
    })


//popup Start
//    $(window).scroll(function () {
//        
//        if ($("body").hasClass("popup-asila")) {
//            if ($(this).scrollTop() > 400) {
//                $('.popup-container').fadeIn();
//                $("body").removeClass("popup-asila");
//            }
//
//        }
//        $('#pp-close').click(function () {
//            $('.popup-container').fadeOut();
//        });
//    });

//popup End

    $(".toggle").click(function () {
        $(".block").toggleClass("expanded");
        $(".content").toggleClass("display");
    });

//jpages  Start
    $("div.holder").jPages({
        containerID: "newsContainer",
        perPage: 14
    });
//jpages  end
//jpages  Start
    $("div.holder").jPages({
        containerID: "latestContainer",
        perPage: 3
    });
//jpages  end

    $("div.holder").jPages({
        containerID: "investorcontainer",
        perPage: 12
    });

    $("div.holder").jPages({
        containerID: "press-container",
        perPage: 10
    });


    wow = new WOW(
            {
                boxClass: 'wow', // default
                animateClass: 'animated', // default
                offset: 0, // default
                mobile: true, // default
                live: true        // default
            }
    )
    wow.init();

//START SMOTH SCROOL JS 
    $('a[href*="#"]:not([href="#"])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: (target.offset().top) - 80
                }, 1000);
                return false;
            }
        }
    });
// 03. END SMOTH SCROOL JS 

//over lay color animation Home page company profile start
    if ($(".section-heading").length) {
        var waypoint6 = new Waypoint({
            element: $(".section-heading"),
            handler: function (direction) {
                $(this.element).addClass("color-border");
            },
            offset: '70%'
        });
    }
//over lay color animation Home page company profile end



//Increase and Decrease font size start
    var $affectedElements = $("section p,section h1,section li,section a"); // Can be extended, ex. $("div, p, span.someClass")
// Storing the original size in a data attribute so size can be reset
    $affectedElements.each(function () {
        var $this = $(this);
        $this.data("orig-size", $this.css("font-size"));
    });
    $("#btn-increase").click(function () {
        changeFontSize(1);
    });

    $("#btn-decrease").click(function () {
        changeFontSize(-1);
    });

    $("#btn-orig").click(function () {
        $affectedElements.each(function () {
            var $this = $(this);
            $this.css("font-size", $this.data("orig-size"));
        });
    });


    function changeFontSize(direction) {
        $affectedElements.each(function () {
            var $this = $(this);
            if (direction > 0) {
                if ($this.css("font-size").slice(0, -2) < 18) {
                    $this.css("font-size", parseInt($this.css("font-size")) + direction);
                }
                ;
            } else {
                if ($this.css("font-size").slice(0, -2) > 12) {
                    $this.css("font-size", parseInt($this.css("font-size")) + direction);
                }
                ;
            }
        });
    }
//Increase and Decrease font size end

    /* show hide search box */
    $(".search_bar").on("click", function () {
        //$(this).closest(".search_bar").toggleClass("close_search");
        $(".search_box").slideToggle(400);
    });
    $(".search_bar").on("click", function () {
        $('body').addClass('home');
    });
    $(".close_search").on("click", function () {
        $('body').removeClass('home');
    });
    /* show hide search box ends */
    $('.modal-header .close ').click(function () {
        $('.add-popup').fadeOut();
    });

    /* Spotlight clese start */
    $('.spot_close').click(function () {
        $('.btn-play').fadeOut();
    });
    /* Spotlight clese end */
    //press-brief-slider start

    var owl11 = $(".press-slider");
    owl11.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 1,
        autoplay: true,
        dots: false,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsiveClass: true
    });




    //press-brief-slider end

//  Main menu
    mainmenu();
    $('.hidden-bar-opener').click(function () {
        console.log("click hela");
        $('.menu-overlay').fadeIn();
    });
    $('.hidden-bar-closer').click(function () {
        $('.menu-overlay').fadeOut();
    });
    $('.menu-overlay').click(function () {
        $('.menu-overlay').fadeOut();
    });
    function mainmenu() {
//Submenu Dropdown Toggle
        if ($('.main-menu li.dropdown ul').length) {
            $('.main-menu li.dropdown').append('<div class="dropdown-btn"></div>');
            //Dropdown Button

            $('.main-menu li.dropdown').on('click', function () {
                debugger
                $(this).prev('ul').slideToggle(500);
                //console.log('clicked');
            });
        }

    }

//Submenu Dropdown Toggle
    if ($('.site-header .navigation li.dropdown ul').length) {
        $('.site-header .navigation li.dropdown').append('<div class="dropdown-btn"></div>');
        //Dropdown Button
        $('.site-header li.dropdown .dropdown-btn').on('click', function () {
            debugger
            $(this).prev('ul').slideToggle(500);
        });
//Disable dropdown parent link
        $('.site-header .navigation li.dropdown > a').on('click', function (e) {
            e.preventDefault();
        });
    }


//Add One Page Nav
    if ($('ul.one-page-nav').length) {
        $('ul.one-page-nav').onePageNav();
    }


// Scroll to Navigation
    if ($('.scroll-to-navigation').length) {

        $(".scroll-to-navigation > li > a").on('click', function (e) {
            var targetSection = $(this).attr('href');
            e.preventDefault();
            var outerParent = $('.scroll-to-navigation > li');
            var targetParent = $(this).parent('li');
            // animate
            $('html, body').animate({
                scrollTop: $(targetSection).offset().top
            }, 1000);
            outerParent.removeClass('current');
            targetParent.addClass('current');
        });
    }


//Hidden Bar Menu Config
    function hiddenBarMenuConfig() {
        var menuWrap = $('.hidden-bar .side-menu');
        // appending expander button
        menuWrap.find('.dropdown').children('a').append(function () {
            return '<button type="button" class="btn expander"><i class="fa fa-angle-down"></i></button>';
        });
// hidding submenu
        menuWrap.find('.dropdown').children('ul').hide();
// toggling child ul
        menuWrap.find('.btn.expander').each(function () {
            $(this).on('click', function () {
                debugger
                $(this).parent() // return parent of .btn.expander (a)
                        .parent() // return parent of a (li)
                        .children('ul').slideToggle();
                $(this).closest('li').siblings().children('ul').slideUp();
                $(this).closest('li').siblings().find('i').removeClass('fa-angle-up').addClass('fa-angle-down');
                // adding class to expander container
                $(this).parent().toggleClass('current');
                // toggling arrow of expander
                $(this).find('i').toggleClass('fa-angle-up fa-angle-down');
                return false;
            });
        });
    }

    hiddenBarMenuConfig();
//Hidden Sidebar
    if ($('.hidden-bar').length) {
        var hiddenBar = $('.hidden-bar');
        var hiddenBarOpener = $('.hidden-bar-opener');
        var hiddenBarCloser = $('.hidden-bar-closer, .menu-overlay');
        $('.hidden-bar-wrapper').mCustomScrollbar();
        //Show Sidebar
        hiddenBarOpener.on('click', function () {
            hiddenBar.addClass('visible-sidebar');
        });
//Hide Sidebar
        hiddenBarCloser.on('click', function () {
            hiddenBar.removeClass('visible-sidebar');
        });

    }
//main-menu end

//news ticker start
//    var nt_example1 = $('.news-scroll').newsTicker({
//        row_height: 90,
//        max_rows: 3,
//        duration: 5000
//    });
//news ticker end

    $('.popup-youtube').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });

    $('.popup-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                //return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
            }
        }
    });


    var owl5 = $(".operation-slider");
    owl5.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 4,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 2,
                nav: false,
                loop: true
            },
            1000: {
                items: 4,
                nav: false,
                loop: true
            }
        }
    });
    $('.c-navigation .c-preview').click(function () {
        owl5.trigger('next.owl.carousel');
    });
    $('.c-navigation .c-next').click(function () {
        owl5.trigger('prev.owl.carousel');
    });



    var owl6 = $(".news-slider");
    owl6.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 1,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        animateOut: 'fadeOut',
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 1,
                nav: false,
                loop: true
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.c-navigation .control-icon-preview').click(function () {
        owl6.trigger('next.owl.carousel');
    });
    $('.c-navigation .control-icon-next').click(function () {
        owl6.trigger('prev.owl.carousel');
    });
    $('.control-icon-playy').on('click', function () {
        owl6.trigger('play.owl.autoplay');
    });
    $('.control-icon-pausee').on('click', function () {
        owl6.trigger('stop.owl.autoplay');
    });

    var owl7 = $(".spotlight-slider");
    owl7.owlCarousel({
        loop: true,
        margin: 0,
        autoWidth: false,
        items: 1,
        autoplay: true,
        //autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        //animateOut: 'fadeOut',
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 1,
                nav: false,
                loop: true
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.slight-right-nav').click(function () {
        owl7.trigger('next.owl.carousel');
    });
    $('.slight-left-nav').click(function () {
        owl7.trigger('prev.owl.carousel');
    });


    var owl15 = $(".spotlight-slider2");
    owl15.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 1,
        autoplay: true,
        autoplaySpeed: 1000,
        //autoplayTimeout: 10000,
        autoplayHoverPause: true,
        responsiveClass: true,
        navigation: true,
        touchDrag: false,
        mouseDrag: false,
        animateOut: 'fadeOut',
        animateIn: 'slideInUp',

    });

    var owl8 = $(".ladlii-slider");
    owl8.owlCarousel({
        loop: true,
        margin: 0,
        autoWidth: false,
        items: 1,
        autoplay: false,
        //autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        //animateOut: 'fadeOut',
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 1,
                nav: false,
                loop: true
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.ladlii-right-nav').click(function () {
        owl8.trigger('next.owl.carousel');
    });
    $('.ladlii-left-nav').click(function () {
        owl8.trigger('prev.owl.carousel');
    });



    var owl9 = $(".cmd-slider");
    owl9.owlCarousel({
        loop: true,
        margin: 0,
        autoWidth: false,
        items: 1,
        autoplay: false,
        //autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        //animateOut: 'fadeOut',
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 1,
                nav: false,
                loop: true
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.pop-box-next').click(function () {
        owl9.trigger('next.owl.carousel');
    });
    $('.pop-box-prev').click(function () {
        owl9.trigger('prev.owl.carousel');
    });

    var owl33 = $(".spot-slider");
    owl33.owlCarousel({
        loop: true,
        margin: 0,
        autoWidth: false,
        items: 1,
        autoplay: true,
        //autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        nav: false,
        dots: false,
        animateOut: 'fadeOut',
        navigation: true,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: true
            },
            600: {
                items: 1,
                nav: false,
                loop: true
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.pop-box-next').click(function () {
        owl33.trigger('next.owl.carousel');
    });
    $('.pop-box-prev').click(function () {
        owl33.trigger('prev.owl.carousel');
    });



    $('.cmd-scroll-pop').slimScroll({
        height: '225px',
        position: 'right',
        railVisible: true,
        color: '#224e7f',
        railColor: '#222',
        railOpacity: 0.3,
        wheelStep: 10,
        alwaysVisible: false
    });


    /* Awards Owl */
    var awards = $('#aw-carousel').owlCarousel({
        loop: true,
        margin: 10,
        autoplay: true,
        responsiveClass: true,
        smartSpeed: 1500,
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            600: {
                items: 1,
                nav: false
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.awards .box-next').click(function () {
        awards.trigger('next.owl.carousel');
    });
    $('.awards .box-prev').click(function () {
        awards.trigger('prev.owl.carousel');
    });
    /* Awards Owl ends */



//journey slider new start


//Pause
    $('.control-icon-pause').click(function () {
        $mq.marquee('pause');
    });

//Resume
    $('.control-icon-play').click(function () {
        $mq.marquee('resume');
    });

    //Pause
    $('.control-icon-pause-e').click(function () {
        $mq2.marquee('pause');
    });

//Resume
    $('.control-icon-play-y').click(function () {
        $mq2.marquee('resume');
    });
    var $mq2 = $('.marquee1').marquee({
        //pauseOnHover: true,
        duration: 23000,
        duplicated: true
    });
    var $mq = $('.marquee').marquee({
        //pauseOnHover: true,
        duration: 30000,
        duplicated: true
    });
    $mq.marquee('pause');
    if ($("#journey").length) {
        var waypoint50 = new Waypoint({
            element: $("#journey"),
            handler: function (direction) {
                $mq.marquee('resume');
            },
            offset: '80%'
        });
    }
//var $mq = $('.marquee').marquee();

//Pause

    $('.nalco-train-cont').on('mouseenter', function () {
        $mq.marquee('pause');
    });


    $('.nalco-train-cont').on('mouseleave', function () {

        if ($('.modal-backdrop').hasClass('in')) {
            $mq.marquee('pause');
        } else {
            $mq.marquee('resume');
        }


    });

    $('.nalco-train-cont').click(function () {
        $mq.marquee('pause');
    });

//Resume
    $('#year-modal .close').click(function () {
        $mq.marquee('resume');
    });

    /* Journey Popup Start*/
    $('.journey-slider .nalco-train-cont').on('click', function () {
        var pop_contnt = $(this).find('.full-content').html();
        var pop_heading = $(this).find('.journey-year').html();
//        $('.modal-body').find('p').html(pop_contnt);
        $('#year-modal').find('.modal-body').html(pop_contnt);
        $('#year-modal').find('.modal-title').html(pop_heading);
    });
    /* Journey Popup end */


//journey slider new end

    /* Board Of directors Start*/
    $('.team-box .plus').on('click', function () {
        //debugger
        var pop_contntt = $(this).closest('.team-box').find('.full-content').html();
        var pop_headingg = $(this).closest('.team-box').find('.person-name').html();
//        $('.modal-body').find('p').html(pop_contnt);
        $('#fromthedesk').find('.modal-body').html(pop_contntt);
        $('#fromthedesk').find('.modal-title').html(pop_headingg);
    });
    /*  Board Of directors end */





    /* Owl achievements */
    var achievements = $('#ac-carousel').owlCarousel({
        loop: true,
        margin: 10,
        responsiveClass: true,
        dots: false,
        nav: false,
        autoplay: true,
        smartSpeed: 1500,
        responsive: {
            0: {
                items: 1,
                nav: false
            },
            600: {
                items: 1,
                nav: false
            },
            1000: {
                items: 1,
                nav: false,
                loop: true
            }
        }
    });
    $('.achievements .box-next').click(function () {
        achievements.trigger('next.owl.carousel');
    });
    $('.achievements .box-prev').click(function () {
        achievements.trigger('prev.owl.carousel');
    });
    /* Owl achievements ends */

    if ($(".banner-slider2").length) {
        var bannerslider2 = $('.banner-slider2').owlCarousel({
            loop: true,
            margin: 0,
            items: 1,
            animateOut: 'fadeOut',
            autoplay: true,
            //autoplayTimeout: 2000,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        });
//        $('.news .box-next').click(function () {
//            awardslider.trigger('next.owl.carousel');
//        });
//        $('.news .box-prev').click(function () {
//            awardslider.trigger('prev.owl.carousel');
//        });
        /* News Carousal */
    }





});



$(window).on("load", function () {
    $(".loader").addClass('hideLoader');
    setTimeout(function () {
        $(".loader").fadeOut();
    }, 1500);

    setTimeout(function () {
        $("#theme-options-left").addClass("active");
        $("#theme-options").addClass("active");
    }, 15000);


    setTimeout(function () {
        $("#theme-options-left").removeClass("active");
        $("#theme-options").removeClass("active");
    }, 20000);

});


/* Change Contrast */
function changeContrast(contrast) {

    if (contrast === 'inactivate') {
        $('body').removeClass('contrast-active');
    } else {
        $('body').addClass('contrast-active');
    }

}

if ($(".full-bar-search-toggle").length) {
    $(document).on('click', '.full-bar-search-toggle', function () {
        $('.full-bar-search-wrap').toggleClass('active');
//        return false;
    });
}
//$('.full-bar-search-toggle').click(function () {
//    $('.full-bar-search-wrap').fadeOut();
//});



/* Check for sticky header */
function stickyHeader() {
    //debugger
    if ($('.stricky').length) {
        var strickyScrollPos = 100;


        if ($(window).width() > 767) {
            if ($(window).scrollTop() > strickyScrollPos) {
                $('.stricky').removeClass('slideIn animated');
                $('.stricky').addClass('stricky-fixed slideInDown animated');
                $('.scroll-to-top').fadeIn(500);
            } else if ($(this).scrollTop() <= strickyScrollPos) {
                $('.stricky').removeClass('stricky-fixed slideInDown animated');
                $('.stricky').addClass('slideIn animated');
                $('.scroll-to-top').fadeOut(500);
            }
        }




    }
    ;
}


//over lay color animation Home page company profile start
if ($(".demo").length) {
    var waypoint6 = new Waypoint({
        element: $(".demo"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
//over lay color animation Home page company profile end


//header animation start
if ($(".center-heading-zero").length) {
    var waypoint51 = new Waypoint({
        element: $(".center-heading-zero"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
if ($(".center-heading-one").length) {
    var waypoint52 = new Waypoint({
        element: $(".center-heading-one"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
//if ($(".wrapper").length) {
//        $(".wrapper").each(function () {
//            var waypoint52 = new Waypoint({
//                element: $(this),
//                handler: function (direction) {
//                    $(this.element).addClass("animate-image-overlay");
//                },
//                offset: '70%'
//            });
//        });
//    }
if ($(".center-heading-two").length) {
    var waypoint53 = new Waypoint({
        element: $(".center-heading-two"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
if ($(".center-heading-three").length) {
    var waypoint54 = new Waypoint({
        element: $(".center-heading-three"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
if ($(".center-heading-four").length) {
    var waypoint55 = new Waypoint({
        element: $(".center-heading-four"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
if ($(".center-heading-five").length) {
    var waypoint55 = new Waypoint({
        element: $(".center-heading-five"),
        handler: function (direction) {
            $(this.element).addClass("animate-image-overlay");
        },
        offset: '70%'
    });
}
//header animation end

if ($("#profile").length) {
    var waypoint7 = new Waypoint({
        element: $("#profile"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("pro");
        },
        offset: '50%'
    });
}
if ($("#journey").length) {
    var waypoint8 = new Waypoint({
        element: $("#journey"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("jou");
        },
        offset: '50%'
    });
}
if ($("#numbers-speak").length) {
    var waypoint8 = new Waypoint({
        element: $("#numbers-speak"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("num");
        },
        offset: '50%'
    });
}
if ($("#our-operation").length) {
    var waypoint8 = new Waypoint({
        element: $("#our-operation"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("ope");
        },
        offset: '50%'
    });
}
if ($("#sustainability").length) {
    var waypoint8 = new Waypoint({
        element: $("#sustainability"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("sus");
        },
        offset: '50%'
    });
}
if ($("#recentnews").length) {
    var waypoint8 = new Waypoint({
        element: $("#recentnews"),
        handler: function (direction) {
            //debugger;
            $("#page-nav").attr("class", "");
            $("#page-nav").addClass("rec");
        },
        offset: '50%'
    });
}






var snippet = {
    version: "1.3",
    // Writes out the given text in a monospaced paragraph tag, escaping
    // & and < so they aren't rendered as HTML.
    log: function (msg, tag) {
        var elm = document.createElement(tag || "p");
        elm.style.fontFamily = "monospace";
        elm.style.margin = "2px 0 2px 0";
        if (Object.prototype.toString.call(msg) === "[object Array]") {
            msg = msg.join();
        } else if (typeof msg === "object") {
            msg = msg === null ? "null" : JSON.stringify(msg);
        } else {
            msg = String(msg);
        }
        elm.appendChild(document.createTextNode(msg));
        document.body.appendChild(elm);
    },

// Writes out the given HTML at the end of the body,
// exactly as-is
    logHTML: function (html) {
        document.body.insertAdjacentHTML("beforeend", html);
    }
};




function stopTrain(train) {
    train.stop();
}


function startTrain(train) {

    debugger
    // var modalH=$('.modal-backdrop').hasClass('in');
    if ($('.modal-backdrop').hasClass('in')) {
        train.stop();
    } else {
        train.start();
    }

}

