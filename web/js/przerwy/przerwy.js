/* 
 author: istockphp.com
 */
jQuery(function($) {

    var pustakolejka = $("<p id='pustakolejka'>Nikogo nie ma w kolejce</p>");
    var pustaprzerwa = $("<p id='pustaprzerwa'>Nikogo nie ma na przerwie</p>");

    function myFunction()
    {
        var konsultant = $('#sesja').load('sesja.php').text();
        var konsultantid = konsultant.replace(/\s/g, "");
        var x = $("#" + konsultantid);
        if ($(x).is(".unable")) {
            alert(konsultant + "\n" + "możesz iść na przerwę");
            myFunction = null;
        }
    };
    function refresh() {

        setTimeout(function() {
            $.ajax({
                url: "refreshkolejka.php",
                cache: false,
                dataType: "html",
                success: function(data) {
                    $('#kolejka').html('<div  class="lead">Kolejka</div>' + "\n" + data);
                    var konsultant = $('#sesja').load('sesja.php').text().trim();
                    var tabelakolejka = $("#kolejka p:contains('" + konsultant + "')");
                    var klucz = $("#kolejka p:contains('" + konsultant + "')").index();
                    var pozycja = $('#kolejka p').eq(klucz);
                    if ((pozycja.length === 1) && (tabelakolejka.length === 1)) {

                        var przepusc = $('<input/>').attr({type: 'button', id: konsultant, value: 'Przepuść', class: 'btn btn-large btn-primary'});

                        $('#button2').html(przepusc);
                        $("input[value|='Przepuść']").click(function() {
                            $.ajax({
                                url: "przepusc.php",
                                type: "POST",
                                cache: false,
                                async: true,
                                data: "action=" + konsultant

                            });
                        });
                    }
                    else
                    {
                        $("input[value|='Przepuść']").remove();
                    }
                    $("#kolejka p")
                            .mouseover(function() {
                                var konsultant = $(this).text();
                                var tmp = konsultant.split(" ");
                                var imie = tmp[0];
                                var nazwisko = tmp[1];

                                $.ajax({
                                    url: "zdjecie.php",
                                    type: "POST",
                                    dataType: "JSON",
                                    async: false,
                                    data: 'imie=' + imie + '&nazwisko=' + nazwisko,
                                    success: function(data) {

                                        var zdjecie = $('<img/>').attr({src: data.link, width: '180px', height: '180px'});
                                        $('#zdjecie').show();
                                        $("#zdjecie").addClass("zdjecie");
                                        $('#zdjecie').html(zdjecie);
                                    }
                                });
                            })
                            .mouseout(function() {
                                $('#zdjecie').hide();
                            });
                }
            });
            $.ajax({
                url: "refreshprzerwa.php",
                cache: false,
                dataType: "html",
                success: function(data) {
                    $('#przerwa').html('<div  class="lead">Przerwa</div>' + "\n" + data);
                }
            });
            $.ajax({
                url: "aktualnylimit.php",
                cache: false,
                dataType: "html",
                success: function(data) {
                    $('#aktualnylimit span').html(data);
                }
            });
            if (myFunction) { //funkcja istnieje
                myFunction();
            }
            ;
            refresh();
        }, 2000);
    }



    $(document).ready(function() {

        $("#zdjecie").removeClass("zdjecie");

        $.ajax({
            url: "new.php",
            type: "POST",
            dataType: "JSON",
            async: false,
            data: "action=checklogin",
            success: function(msg) {

                if (msg.type !== "empty")
                {
                    var isSuccess = msg.type;
                    var myString = isSuccess.toString().replace(/[\r\n]+/g, '');
                    var tmp = myString.split(",");
                    for (var i = 0; i < tmp.length; i++) {
                        $('#schowanakolejka').append("<p  id='" + tmp[i] + "'>" + tmp[i] + "</p>");
                    }
                }
                var x = $("#schowanakolejka p:contains('" + msg.sesja + "')");
                if (x.length === 1) {
                    var przerwa = $('<input/>').attr({type: 'button', id: msg.sesja, value: 'Przerwa', class: 'btn btn-large btn-success'});
                    var usunzkolejki = $('<input/>').attr({type: 'button', id: msg.sesja, value: 'Usuń z kolejki', class: 'btn btn-large btn-danger'});
                    $('#buttony').append(przerwa);
                    $('#button1').append(usunzkolejki);
                    $("#topopup").hide();
                }
                if (msg.przerwy !== "empty")
                {
                    isSuccess = msg.przerwy;
                    myString = isSuccess.toString().replace(/[\r\n]+/g, '');
                    var tmp = myString.split(",");
                    for (var i = 0; i < tmp.length; i++) {
                        $('#schowanaprzerwa').append("<p  id='" + tmp[i] + "'>" + tmp[i] + "</p>");
                    }
                    var x = $("#schowanaprzerwa p:contains('" + msg.sesja + "')");
                    if (x.length === 1)
                    {
                        var usunzprzerwy = $('<input/>').attr({type: 'button', id: msg.sesja, value: 'Usuń z Przerwy', class: 'btn btn-large btn-danger'});
                        $('#buttony').append(usunzprzerwy);
                        $("#topopup").hide();
                    }
                }
            }
        });
        user();
        przepusc();
        przerwa();
        usunzprzerwy();
        usunzkolejki();

        if (myFunction) { //funkcja istnieje
            myFunction();
        }
        ;
        refresh();
    });


    /* event for close the popup */
    $("div.close").hover(
            function() {
                $('span.ecs_tooltip').show();
            },
            function() {
                $('span.ecs_tooltip').hide();
            }
    );

    $("div.close").click(function() {
        disablePopup();  // function close pop up
    });

    $(this).keyup(function(event) {
        if (event.which == 27) { // 27 is 'Ecs' in the keyboard
            disablePopup();  // function close pop up
        }
    });

    $("div#backgroundPopup").click(function() {
        disablePopup();  // function close pop up
    });


    function user() {
        $("input#topopup").click(function() {

            if (navigator.appName == "Microsoft Internet Explorer") {
                var wshell = new ActiveXObject("WScript.Shell");
                var username = wshell.ExpandEnvironmentStrings("%USERNAME%");

                zaloguj(username);

            }
            else {
                loading(); // loading
                setTimeout(function() { // then show popup, deley in .5 second
                    loadPopup(); // function show popup 
                }, 500); // .5 second
                $("#livebox").click(function() {
                    var username = $("#br").val().trim();
                    zaloguj(username);
                    return false;


                });

            }
        });

    }


    function zaloguj(username) {
        $.ajax({
            url: "imienazwisko.php",
            type: "POST",
            dataType: "JSON",
            async: false,
            data: 'login=' + username,
            success: function(msg)
            {

                if (msg.imie) {
                    var wier = 'imie=' + msg.imie + '&nazwisko=' + msg.nazwisko;

                    $.ajax({
                        url: "konsultant.php",
                        type: "POST",
                        dataType: "JSON",
                        async: false,
                        data: wier,
                        success: function(msges) {

                            if (msges.sesja)
                            {
                                $('#schowanakolejka').append("<p>" + msges.sesja + "</p><br/>");
                                $("#topopup").hide();
                                $(".pusty").remove();
                                var przerwa = $('<input/>').attr({type: 'button', id: msges.sesja, value: 'Przerwa', class: 'btn btn-large btn-success'});
                                var usunzkolejki = $('<input/>').attr({type: 'button', id: msges.sesja, value: 'Usuń z kolejki', class: 'btn btn-large btn-danger'});

                                $('#buttony').append(przerwa);
                                $('#button1').append(usunzkolejki);


                                $("#br").val("");                              
                                disablePopup();
                                $('#topopup').hide();
                            }
                            else if (msges.przerwa) {

                                var konsultantid = msges.przerwa.replace(/\s/g, "");
                                $('#schowanaprzerwa').append("<p>" + msges.przerwa + "</p><br/>");
                                $('#schowanaprzerwa p').last().attr({id: konsultantid});
                                var usunzprzerwy = $('<input/>').attr({type: 'button', id: msges.przerwa, value: 'Usuń z Przerwy', class: 'btn btn-large btn-danger'});
                                $('#buttony').append(usunzprzerwy);
                                $("input[value|='Przerwa']").remove();
                                $("input[value|='Usuń z kolejki']").remove();
                                //  $(pustaprzerwa).remove();
                                $('#topopup').hide();
                            }
                        }
                    });
                }
                else if (msg.pusta === "pusta") {
                    alert('Poproś administratora przerw o dodanie twojej br-ki');
                }
            }
        });
        //   });
        //  }
        przerwa();
        usunzprzerwy();
        usunzkolejki();
        przepusc();
        if (myFunction) { //funkcja istnieje
            myFunction();
        }
        ;
        refresh();
        //  });
    }
    /************** start: functions. **************/
    function loading() {
        $("div.loader").show();
    }
    function closeloading() {
        $("div.loader").fadeOut('normal');
    }

    var popupStatus = 0; // set value

    function loadPopup() {
        if (popupStatus == 0) { // if value is 0, show popup
            closeloading(); // fadeout loading        
            $("#toPopup").fadeIn(0500); // fadein popup div
            $("#backgroundPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
            $("#backgroundPopup").fadeIn(0001);
            popupStatus = 1; // and set value to 1
        }
    }

    function disablePopup() {
        if (popupStatus == 1) { // if value is 1, close popup
            $("#toPopup").fadeOut("normal");
            $("#backgroundPopup").fadeOut("normal");
            popupStatus = 0;  // and set value to 0
        }
    }
    function przerwa() {
        $("input[value|='Przerwa']").click(function() {

            var konsultant = $(this).attr('id');
            var konsultantid = konsultant.replace(/\s/g, "");
            x = $("#" + konsultantid);
            if ($(x).is(".unable")) {
                $.ajax({
                    url: "przerwa.php",
                    type: "POST",
                    dataType: "JSON",
                    async: false,
                    data: "action=" + konsultant,
                    success: function(msg) {
                        if (msg)
                        {                      
                            var isSuccess = msg.type;
                            var myString = isSuccess.toString().replace(/[\r\n]+/g, '');
                            var tmp = myString.split(",");
                            for (var i = 0; i < tmp.length; i++) {
                                $('#schowanaprzerwa').append("<p>" + tmp[i] + "</p><br/>");

                            }
                            var usunzprzerwy = $('<input/>').attr({type: 'button', id: konsultant, value: 'Usuń z Przerwy', class: 'btn btn-large btn-danger'});
                            $('#button1').append(usunzprzerwy);
                            $("input[value|='Przerwa']").remove();
                            $("input[value|='Usuń z kolejki']").remove();
                            $(pustaprzerwa).remove();
                            $('#schowanaprzerwa p').last().attr({id: konsultantid});
                        }
                    }
                });
                usunzprzerwy();
                $("#schowanakolejka p:contains('" + konsultantid + "')").remove();
                var x = $('#schowanakolejka p');
                if (x.length == 0) {
                    $(pustakolejka).appendTo("#schowanakolejka");
                }
            }
            else
                alert('musisz jeszcze poczekać ');
        });

    }
    function usunzprzerwy() {
        $("input[value|='Usuń z Przerwy']").click(function() {
            var konsultant = $(this).attr('id');
            $.ajax({
                url: "usunzprzerwy.php",
                type: "POST",
                dataType: "JSON",
                async: false,
                data: "action=" + konsultant,
                success: function(msg) {

                    if (msg.type == "empty")
                    {
                        $("#schowanaprzerwa p:contains('" + konsultant + "')").remove();
                        $("input[value|='Usuń z Przerwy']").remove();
                        $("#topopup").show();
                        $("#proba").load('sesjadestroy.php');
                        location.reload();
                    }

                }

            });
            var x = $('#schowanaprzerwa p');
            if (x.length == 0) {

                $(pustaprzerwa).appendTo("#schowanaprzerwa");


            }

        });
    }
    function usunzkolejki() {
        $("input[value|='Usuń z kolejki']").click(function() {
            var konsultant = $(this).attr('id');
            $.ajax({
                url: "usunzkolejki.php",
                type: "POST",
                dataType: "JSON",
                async: false,
                data: "action=" + konsultant,
                success: function(msg) {

                    if (msg.type == "empty")
                    {
                        $("#schowanakolejka p:contains('" + konsultant + "')").remove();
                        $("input[value|='Usuń z kolejki']").remove();
                        $("input[value|='Przerwa']").remove();
                        $("#topopup").show();
                        $("#proba").load('sesjadestroy.php');
                        location.reload();
                    }

                }

            });

            var x = $('#schowanakolejka p');
            if (x.length == 0) {

                $(pustakolejka).appendTo("#schowanakolejka");


            }

        });

    }
    function przepusc() {
        $("input[value|='Przepuść']").click(function() {

            var konsultant = $(this).attr('id');
            $.ajax({
                url: "przepusc.php",
                type: "POST",
                dataType: "JSON",
                async: false,
                data: "action=" + konsultant,
                success: function(msg) {

                    alert(msg.kolejka);

                }
            });
        });

    }
    /************** end: functions. **************/
}); // jQuery End