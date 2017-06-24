$(document).ready(function()  {
    jQuery.validator.addMethod("kodpocztowy", function(value, element) {
        return this.optional(element) || /^\d{2}-\d{3}$/.test(value);
    }, "Wprowadź kod pocztowy w formacie XX-XXX");


    $.validator.addMethod("checklogin", function(username) {
        var login = $("#fos_user_registration_form_username").val();
        var isSuccess = false;
        $.ajax({
            url: "/Symfony2_8/web/app_dev.php/ajax",
            type: "POST",
            async: false,
            data: "action=checklogin&login=" + login,
            success: function(msg) {

                if (msg == 0)
                {
                    isSuccess = false;
                }
                else
                {

                    isSuccess = true;
                }
            }


        });

        return isSuccess;
    }, "Login jest już zajęty, wprowadź inny");

    jQuery("#formularz").validate({
        messages: {
            'fos_user_registration_form[imie]': {
                required: "Proszę uzupełnić pole Imię.",
                minlength: jQuery.format("Musi być minimum {0} znaków.")
            },
            'fos_user_registration_form[email]': {
                required: "Proszę uzupełnić pole Adres e-mail.",
                email: "Prosze podać prawidłowy adres e-mail, w formacie xxxx@xxxx.xx."
            },
            'fos_user_registration_form[telefon]': {
                number: "Proszę podać numer telefonu składający z samych cyfr bez spacji.",
                required: "Proszę uzupełnić pole Nr telefonu."

            },
            'fos_user_registration_form[nip]': {
                required: "Proszę uzupełnić pole NIP.",
                digits: "Proszę wprowadzić ciąg 10 cyfr",
                minlength: jQuery.format("Wprowadzono za mało cyfr. NIP składa się z {0} cyfr."),
                maxlength: jQuery.format("Wprowadzono za dużo cyfr. NIP składa się z {0} cyfr.")
            },
            'fos_user_registration_form[plainPassword][first]': {
                required: "Proszę wprowadzić hasło",
                minlength: jQuery.format("Wprowadzono za mało znaków. Hasło powinno składać się z min. {0} znaków.")
            },
            'fos_user_registration_form[plainPassword][second]': {
                required: "Proszę potwierdzić hasło",
                equalTo: "Hasło nie zgadza się z poprzednim"
            },
            'fos_user_registration_form[username]': {
                required: "Proszę wprowadzić login"
            },
            'fos_user_registration_form[kodpocztowy]': {
                required: "Proszę uzupełnić pole kod pocztowy."
            }

        }
    });


    
    
});



$(document).ajaxStart(function() {
    // alert('dziala');
     $('#loading').css('display', 'block');
    });
$(document).ajaxComplete(function() {
     $('#loading').css('display', 'none');
    });
 if (navigator.appName == "Microsoft Internet Explorer") {
     
     
 }


