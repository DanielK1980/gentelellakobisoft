jQuery(function($) {
     $(document).ready(function() {
    function refresh() {
        setTimeout(function() {
              
            $.ajax({
                url: "/Symfony2_8/web/app_dev.php/konsultant/ajaxprzerwa",
                cache: false,
                dataType: "html",
                success: function(data) {
                    if (data === 'ok'){
                      $('span#komunikat').html("<img style='position: absolute; left: 505px;top:49px; height: 20px; width: 20px;' src='/Symfony2_8//web/images/gif.png'>");  
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

    /************** end: functions. **************/
}); // jQuery End


