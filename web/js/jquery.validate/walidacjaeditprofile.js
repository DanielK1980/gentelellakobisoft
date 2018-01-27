jQuery(window).load(function(){
    jQuery.validator.addMethod("kodpocztowy", function(value, element) { 
  return this.optional(element) || /^\d{2}-\d{3}$/.test(value); 
}, "Wprowadź kod pocztowy w formacie XX-XXX");


$.validator.addMethod("checklogin", function(username){
    var login = $("#fos_user_profile_form_username").val();
    
    var zalogowanojako = $(".profile_info h2").text().trim();
    // var res = zalogowanojako.split(" ");
    // var log = res[2];
    var isSuccess = false;
    $.ajax({
          url: "/Symfony2_8_new/web/app_dev.php/ajax2",
          type: "POST",
          async: false,
          data: "action=checklogin&login="+login+"&log="+zalogowanojako,
          success:  function(msg) { 

      if(msg ==0)
      {
  isSuccess=false;
      }
      else 
      {

       isSuccess=true;
      }
        }


       });

        return isSuccess;
         },"Login jest już zajęty, wprowadź inny");

    jQuery("#formularz").validate({
      messages: {
        'fos_user_profile_form[imie]':{
          required: "Proszę uzupełnić pole Imię.",
          minlength: jQuery.format("Musi być minimum {0} znaków.")
        },
        'fos_user_profile_form[email]':{
          required: "Proszę uzupełnić pole Adres e-mail.",
          email: "Prosze podać prawidłowy adres e-mail, w formacie xxxx@xxxx.xx."
        },
        'fos_user_profile_form[telefon]':{
          number: "Proszę podać numer telefonu składający z samych cyfr bez spacji.",
          required: "Proszę uzupełnić pole Nr telefonu."
          
        },
        'fos_user_profile_form[nip]':{
          required: "Proszę uzupełnić pole NIP.",  
          digits: "Proszę wprowadzić ciąg 10 cyfr",
          minlength: jQuery.format("Wprowadzono za mało cyfr. NIP składa się z {0} cyfr."),
          maxlength: jQuery.format("Wprowadzono za dużo cyfr. NIP składa się z {0} cyfr.")
        },
        'fos_user_profile_form[plainPassword][first]':{
          required: "Proszę wprowadzić hasło",  
          minlength: jQuery.format("Wprowadzono za mało znaków. Hasło powinno składać się z min. {0} znaków.")
        },
        'fos_user_profile_form[plainPassword][second]':{  
          required: "Proszę potwierdzić hasło",   
          equalTo: "Hasło nie zgadza się z poprzednim"
        },
        'fos_user_profile_form[username]':{ 
          required: "Proszę wprowadzić login"   
        },
       'fos_user_profile_form[kodpocztowy]':  {
         required: "Proszę uzupełnić pole kod pocztowy."
        }
        
      }
    });
    
    
  }); 