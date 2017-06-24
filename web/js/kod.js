$(document).ready(function () {

    var current_location = window.location.href.split('/'); // ['http:', '', '', 'domain.com', 'page.html']

    var found1 = $.inArray("konsultant", current_location) > -1;
    if (found1) {
        $('#menuadmin > li:nth-child(1)').addClass('current');
    }
    var found2 = $.inArray("baza", current_location) > -1;
    if (found2) {
        $('#menuadmin > li:nth-child(2)').addClass('current');
    }
    var found3 = $.inArray("grafik", current_location) > -1;
    if (found3) {
        $('#menuadmin > li:nth-child(3)').addClass('current');
    }
    var found4 = $.inArray("dzialy", current_location) > -1;
    if (found4) {
        $('#menuadmin > li:nth-child(4)').addClass('current');
    }
     var found5 = $.inArray("category", current_location) > -1;
    if (found5) {
        $('#menuadmin > li:nth-child(5)').addClass('current');
    }
    
    var found6 = $.inArray("produkty", current_location) > -1;
    if (found6) {
        $('#menuadmin > li:nth-child(6)').addClass('current');
    }
    var found7 = $.inArray("statrap", current_location) > -1;
    if (found7) {
        $('#menuadmin > li:nth-child(7)').addClass('current');
    }
   
    var found8= $.inArray("przerwy", current_location) > -1;
    if (found8) {
        $('#menuadmin > li:nth-child(8)').addClass('current');
    }
    var found9 = $.inArray("profile", current_location) > -1;
    if (found9) {
        $('#menuadmin > li:nth-child(9)').addClass('current');
    }
    var current = window.location.pathname;
    var arr1 = current.split("/");
    var link1 = arr1[6];


    $('.nav.nav-pills li a').each(function () {
        var link = $(this).attr('href');
        var arr2 = link.split("/");
        var link2 = arr2[6];
        if (link1 === link2) {
            $(this).parent().addClass('active');

        }
    });
    $(".nav.nav-pills li").on("click", function () {
        $(".nav.nav-pills li").removeClass("active");
        $(this).addClass("active");
    });

    // Zamknięcie przeglądarki 
    window.addEventListener("beforeunload", function (e) {

        $.ajax({
            url: "/Symfony2_8/web/app_dev.php/konsultant/browser",
            type: "POST",
            async: false,
            data: "action=checklogin"

        });                           //Webkit, Safari, Chrome
    });

});