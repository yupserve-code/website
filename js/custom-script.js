$(function () {
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


//    $(".user").on("click", function () {
//        $('.user').addClass('home');
//    });
//
//    $(".user").on("click", function () {
//        $('.user').removeClass('home');
//    });



    $(".user").on("click", function (e) {
        $(".user").addClass("home");
        e.stopPropagation()
    });
    $(document).on("click", function (e) {
        if ($(e.target).is(".user") === false) {
            $(".user").removeClass("home");
        }
    });





    var owl1 = $(".banner-slider");
    owl1.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 1,
        dots: false,
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
//    $('.c-navigation .c-preview').click(function () {
//        owl1.trigger('next.owl.carousel');
//    });
//    $('.c-navigation .c-next').click(function () {
//        owl1.trigger('prev.owl.carousel');
//    });

var owl1 = $(".home-services");
    owl1.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 6,
		smartSpeed: 1200,
        dots: false,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsiveClass: true,
        navigation: true,
        responsive: {
            0: {
                items: 2,
                nav: true,
                loop: true
            },
            600: {
                items: 4,
                nav: true,
                loop: true
            },
            1000: {
                items: 6,
                nav: true,
                loop: true
            }
        }
    });


    var owl2 = $(".book-now-slider");
    owl2.owlCarousel({
        loop: true,
        margin: 30,
        autoWidth: false,
        items: 1,
        dots: false,
        autoplay: true,
        autoplayTimeout: 3000,
        animateOut: 'fadeOut',
        touchDrag: false,
        mouseDrag: false,
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


// animation scroll reveal start

    window.sr = ScrollReveal();

    sr.reveal(".reveal-bottom", {
        origin: "bottom",
        distance: "20px",
        duration: 500,
        delay: 400,
        opacity: 1,
        scale: 0,
        easing: "linear",
        reset: !0
    }), sr.reveal(".reveal-top", {
        origin: "top",
        distance: "20px",
        duration: 500,
        delay: 400,
        opacity: 1,
        scale: 0,
        easing: "linear",
        reset: !0
    }), sr.reveal(".reveal-left", {
        origin: "left",
        distance: "50px",
        duration: 300,
        delay: 0,
        opacity: 1,
        scale: 0,
        easing: "linear"
    }), sr.reveal(".reveal-left-delay", {
        origin: "left",
        distance: "50px",
        duration: 300,
        delay: 300,
        opacity: 1,
        scale: 0,
        easing: "linear"
    }), sr.reveal(".reveal-right", {
        origin: "right",
        distance: "50px",
        duration: 600,
        delay: 500,
        opacity: 1,
        scale: 0,
        easing: "linear"
    }), sr.reveal(".reveal-right-fade", {
        origin: "right",
        distance: "50px",
        distance: "50px",
        duration: 800,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".reveal-left-fade", {
        origin: "left",
        distance: "50px",
        duration: 800,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInRight", {
        origin: "right",
        distance: "50px",
        duration: 500,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInRightDelay", {
        origin: "right",
        distance: "20px",
        duration: 1e3,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }, 500), sr.reveal(".fadeIn", {
        origin: "bottom",
        distance: "0",
        duration: 900,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInScale", {
        origin: "bottom",
        distance: "0",
        duration: 500,
        delay: 0,
        opacity: 0,
        scale: .5,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInBottom", {
        origin: "bottom",
        distance: "40px",
        duration: 500,
        delay: 0,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInBottomDelay", {
        origin: "bottom",
        distance: "40px",
        duration: 500,
        delay: 200,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInBottomDelay-2", {
        origin: "bottom",
        distance: "40px",
        duration: 500,
        delay: 500,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    }), sr.reveal(".fadeInBottomDelay-3", {
        origin: "bottom",
        distance: "40px",
        duration: 500,
        delay: 600,
        opacity: 0,
        scale: 0,
        easing: "linear",
        mobile: !1
    });
// animation scroll reveal start



});


