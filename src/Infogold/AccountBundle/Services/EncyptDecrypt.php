<?php

namespace Infogold\AccountBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Infogold\AccountBundle\Services\MainAdmin;

class AllegroLogin {

    protected $user;
    protected $action;
    protected $pass;

    public function __construct(MainAdmin $user) {
        $this->user = $user;
    }

    // Loguje do Allegro
    public function encrypt_decrypt($action, $pass) {

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = '$eRw!$';
        $key = hash('sha256', $secret_key);

        if ($this->action == 'encrypt') {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
            $output = openssl_encrypt($this->pass, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($this->action == 'decrypt') {
            $iv = $this->user->getMainAdmin()->getAllegro()->getIv();
            $output = openssl_decrypt(base64_decode($this->pass), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    protected function key() {
        return '$eRw!$';
    }

}
