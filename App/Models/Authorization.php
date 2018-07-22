<?php   //класс для Авторизации

namespace App\Models;


class Authorization
{
    protected $username;
    protected $db;
    protected $session;


    public function __construct()
    {
        $this->db = new DB();

        $this->session = new Session();

        $us = $this->getUserSession();


        if ( $this->findLogin($us) ) {

            $this->username = $us;
        }


        if ( !isset( $this->username ) ) {

            $this->username = null;
        }
    }


    protected function getUserSession()
    {
        $us = $this->session->getParameter('username');

        if ( is_string($us) ) {

            return $us;
        }

    }


    public function getUsername()
    {
        return $this->username;
    }


    protected function findLogin($login)
    {
        $sql = 'SELECT login FROM admin WHERE login=:log';

        $ret = [':log' => $login];

        $arr = $this->db->query($sql, $ret);

        if ( is_array($arr) ) {
            if ( isset( $arr[0] ) ) {
                if ( isset( $arr[0]['login'] ) ) {
                    if ( $login === $arr[0]['login'] ) {

                        return true;
                    }
                }
            }
        }

        return false;
    }


    protected function findAdmin($login)
    {
        $sql = 'SELECT login, hashpass FROM admin WHERE login=:log';

        $ret = [':log' => $login];

        $arr = $this->db->query($sql, $ret);

        if ( is_array($arr) ) {
            if ( isset( $arr[0] ) ) {
                if ( isset( $arr[0]['login'], $arr[0]['hashpass'] ) ) {
                    if ( $login === $arr[0]['login'] ) {

                        return new Admin( $arr[0]['login'], $arr[0]['hashpass'] );
                    }
                }
            }
        }
    }


    public function authorization( string $login, string $password )
    {
        $ad = $this->findAdmin($login);
        $us = $ad->getLogin();
        $h = $ad->getHashPass();

        if ( $login === $us ) {
            if ( password_verify($password, $h ) ) {
                if ( $this->session->setParameter('username', $login) ) {

                    $this->username = $login;

                    return true;
                }
            }
        }

        return false;
    }


    public function out()
    {
        $this->session->destroy();
        $this->username = null;

    }

}