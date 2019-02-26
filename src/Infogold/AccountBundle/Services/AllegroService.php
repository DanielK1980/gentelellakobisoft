<?php

namespace Infogold\AccountBundle\Services;

use Infogold\AccountBundle\Services\MainAdmin;
use Symfony\Component\HttpFoundation\Session\Session;
use GuzzleHttp\Client;

class AllegroService {

    protected $user;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, MainAdmin $user) {
        $this->entityManager = $entityManager;
        $this->user = $user;

        define('LINK', "https://allegro.pl.allegrosandbox.pl/"); //nowy wsdl,
        define('REDIRECT_URL', "http://localhost/symfony2_8_new/web/app_dev.php/user/produkty/");
        define('COUNTRY', 1);
    }

    /*
      public function getModuleAllegro($name, $arr) {
      $user = $this->user->getMainAdmin();
      try {
      $client = new \SoapClient(LINK);
      $argumenty = array(
      'webapiKey' => $user->getAllegro()->getAllegroKeyWebApi(), 'countryId' => COUNTRY);

      foreach ($arr as $key => $addargument){

      $argumenty[$key] = $addargument;
      }
      $items = $client->{"$name"}($argumenty);

      } catch (\SoapFault $error){
      $items = $this->get('session')->getFlashBag()->add('error', "getModuleAllegro: $error->faultstring");
      }
      return $items;
      }
     */

    public function getToken($code) {

        $user = $this->user->getMainAdmin();

        $credentials = base64_encode($user->getAllegro()->getAllegroClientID() . ":" . $user->getAllegro()->getAllegroClientSecret());

        if ($code) {

            $client = new Client([             
                "base_uri" => LINK,              
                "timeout" => 2.0,
            ]);
            $response = $client->request("POST", "auth/oauth/token?grant_type=authorization_code&code=$code&redirect_uri=" . REDIRECT_URL, [
                "headers" => [
                    "Authorization" => "Basic " . $credentials,
                ]
            ]);

            $wynik = $response->getStatusCode();

            if ($wynik == "200") {
                $body = $response->getBody()->getContents();
                
                $allegro = $this->entityManager->getRepository('InfogoldAccountBundle:Allegro')->findOneBy(array('UserAllegro' => $user->getId()));
                   
                
            }
        }
    }

    public function refreshToken() {
        
    }

    public function SessionAllegro() {
        $sesja = null;
        /*
          $session = new Session();

          if(!$session->has("sessionToken")){

          $sesja = $this->getToken();

          if(isset($sesja["alert"]) && $sesja["alert"] == "success"){

          $session->set("sessionToken", $sesja["sesja"]->sessionHandlePart);
          $session->set("sessionId", $sesja["sesja"]->userId);
          $session->set("serverTime", strtotime("+720 minutes", $sesja["sesja"]->serverTime));
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

          } */

        return $sesja;
    }

    public function makeRequest($method, array $postFields = array(), $raw = false) {
        $headers = array(
            'Content-Type: application/json; charset=utf-8'
        );
        if ($login == false && $password == false && $shop != false) {

            /* /* $conn = $this->app->db();
              $stmt = $conn->prepare('SELECT login_wfirma,password_wfirma FROM shops where shop =?');
              $stmt->execute(array(
              $shop
              ));

              $loginpass = $stmt->fetch();

              $login = $this->decode($loginpass['login_wfirma']);
              $password = $this->decode($loginpass['password_wfirma']);
             * 
             */
        }

        if ($login != false && $password != false) {

            $module = substr($method, 0, strpos($method, '/'));
            $postFields = array($module => $postFields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, min(300, ini_get("max_execution_time")));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, min(100, ini_get("max_execution_time")));
            curl_setopt($ch, CURLOPT_URL, self::API_URI . $method . "?inputFormat=json&outputFormat=json");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
            //curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);

            $response = curl_exec($ch);
            if ($raw == false) {
                $resp = json_decode($response, true);

                if ($resp['status']['code'] != self::STATUS_OK) {
                    //  $errorMessage = $this->_getErrorMessage($response);
                    //  echo 'error';
                    //  $log = Zend_Registry::get(System_Bootstrap::REGISTRY_INDEX_LOG_ERROR);
                    //  $log->log($errorMessage, Zend_Log::ERR);
                    //   throw new Wfirma_Exception($errorMessage);
                    return "Autoryzacja się nie powiodła - błąd przy próbie połączenia z wfirma.pl";
                }
            }
            curl_close($ch);

            return $resp;
        } else {
            return "błąd przy próbie połączenia z wfirma.pl";
        }
    }

}
