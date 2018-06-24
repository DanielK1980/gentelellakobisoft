/*
jQuery(function($) {
     $(document).ready(function() {
    function refresh() {
        setTimeout(function() {
              
            $.ajax({
                url: "/Symfony2_8_new/web/app_dev.php/konsultant/ajaxprzerwa",
                cache: false,
                dataType: "html",
                success: function(data) {
                    if (data === 'ok'){
                      $('span#komunikat').html("<img style='position: absolute; left: 505px;top:49px; height: 20px; width: 20px;' src='/Symfony2_8_new/web/images/gif.png'>");  
                    } 
                    else
                    {
                          $('span#komunikat').html("");
                    }                                    
                }
            });          
            refresh();
        }, 60000);
    }
        refresh();
    });

   
}); 

*/

  
 $(document).ready(function () {
      
      window.localLinkClicked = false;

$("a").on("click", function() {
    var url = $(this).attr("href");

    // check if the link is relative or to your domain
    if (! /^http?:\/\/./.test(url) || /http?:\/\/localhost\symfony2_8_new/.test(url)) {
        window.localLinkClicked = true;
       //alert("kliknięty link");
    }
});
 window.addEventListener("beforeunload", function (e) {
       if (window.localLinkClicked) {
        // alert("kliknięty link");
    } else {
         $.ajax({
            url: "/Symfony2_8_new/web/app_dev.php/konsultant/browser",
            type: "POST",
            async: false,
            data: "action=checklogin"

        });
    }
   //  console.log('event');//Webkit, Safari, Chrome
});

}); 
   
  