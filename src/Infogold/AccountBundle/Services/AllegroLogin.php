<?php

namespace Infogold\AccountBundle\Services;

use Infogold\AccountBundle\Services\MainAdmin;
use Symfony\Component\HttpFoundation\Session\Session;

class AllegroLogin {

    protected $user;
    protected $link;

    public function __construct(MainAdmin $user) {
        $this->user = $user;
        define('LINK', "http://webapi.allegro.pl.allegrosandbox.pl/service.php?wsdl"); //nowy wsdl,
        //define('LOGIN', 'DanielK1980');
        //define('PASSWORD', 'Fiodor11!');
        //define('KEY', '28045b9b');
        define('CRYPT', 'aes-128-cfb');
        define('COUNTRY', 1);
        define('SYSVAR', 1);
    }
    
    public function SessionAllegro() {
        $sesja = null;
        
         $session = new Session();
         
         if(!$session->has("sessionHandlePart")){
             
               $sesja = $this->LoginToAllegro();
               
               if(isset($sesja["alert"]) && $sesja["alert"] == "success"){
                   
               $session->set("sessionHandlePart", $sesja["sesja"]->sessionHandlePart);
               $session->set("sessionId", $sesja["sesja"]->userId);
               $session->set("serverTime", strtotime("+58 minutes", $sesja["sesja"]->serverTime));
               $session->set("allegroId", $sesja["allegro"]);
               }
         }else{
             if($session->has("serverTime") && $session->get("serverTime") < time()){
                 
               $sesja = $this->LoginToAllegro();
               
               if(isset($sesja["alert"]) && $sesja["alert"] == "success"){
                   
               $session->set("sessionHandlePart", $sesja["sesja"]->sessionHandlePart);
               $session->set("sessionId", $sesja["sesja"]->userId);
               $session->set("serverTime", strtotime("+58 minutes", $sesja["sesja"]->serverTime));
               $session->set("allegroId", $sesja["allegro"]);
               
               } 
             }
             else{
                 $endTime = date('H:i:s', $session->get("serverTime"));
                 $sesja = array("allegro" => $session->get("allegroId"),"alert" =>"success","message"=>"Sesja allegro trwa do ". $endTime);
             }
             
         }
        
        return $sesja;
    }

    // Loguje do Allegro
    public function LoginToAllegro() {
        $user = $this->user->getMainAdmin();

        if ($user->getEnableAllegro()) {
                
            $allegro = $user->getAllegro();

            $wynik = array();
            
            if($allegro){
            try {
                $client = new \SoapClient(LINK);

                $version_params = array('sysvar' => SYSVAR, 'countryId' => COUNTRY, 'webapiKey' => $allegro->getAllegroKeyWebApi());

                $version = (array) ($client->doQuerySysStatus($version_params));

                $original_password = openssl_decrypt($allegro->getPasswordAllegro(), CRYPT, $this->key(), $options = 0, $allegro->getIv());

                $session_params = array('userLogin' => $allegro->getLoginAllegro(), 'userPassword' => $original_password, 'countryCode' => COUNTRY,
                    'webapiKey' => $allegro->getAllegroKeyWebApi(), 'localVersion' => $version['verKey']);

                $sessionAllegro = $client->doLogin($session_params);
                
              //  $session->set("sessionHandlePart", $sessionAllegro->sessionHandlePart);
               // $session->set("serverTime", $sessionAllegro->serverTime);

                $wynik = array("sesja"=>$sessionAllegro, "alert" =>"success","message"=>"Zalogowano poprawnie do Allegro jako: " . $allegro->getLoginAllegro(),"allegro"=>$allegro->getId());
               
            } catch (\SoapFault $error) {
                $wynik = array("alert" => "error" ,"message" =>"Błąd logowania w Allegro: $error->faultstring", "allegro"=>$allegro->getId());
            }
            }else{
                 $wynik = array("login" => "Zalogu się do allegro");
            }
                                
        } else {
            $wynik = array("disable" => "Brak aktywnego modułu allegro");
        }               
        return $wynik;
    }

    protected function key() {
        return '$eRw!$';
    }

}
