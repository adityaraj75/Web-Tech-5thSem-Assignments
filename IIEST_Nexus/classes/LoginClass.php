<?php

require_once("DataBase.php");
require_once("variables.php");

class LoginClass
{
    public static function isLoggedIn()
    {
        if (isset($_COOKIE['SNID']))
        {
            $res = DataBase::query('SELECT userid FROM '.DataBase::$token_table_name.
                                    ' WHERE token=:token',
                                    array(':token'=>sha1($_COOKIE['SNID'])));
            if ($res['executed'])
            {
                if(count($res['data'])===0)
                    return False;
                $userid = $res['data'][0]['userid'];
                if(isset($_COOKIE['SNID_']))
                {
                    return $userid;
                }
                else
                {
                    $crypto_strong = True;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
                    DataBase::query('INSERT INTO '.DataBase::$token_table_name.' VALUES (\'\', :token, :userid)',
                                    array(':token'=>sha1($token), ':userid'=>$userid));
                    DataBase::query('DELETE FROM '.DataBase::$token_table_name.' WHERE token=:token',
                                    array(':token'=>sha1($_COOKIE['SNID'])));

                    setcookie("SNID", $token, time() + 60*60*24*7, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                    setcookie("SNID_", '1', time() + 60*60*24*3, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                    return $userid;
                }
            }
        }
        return False;
    }
}
?>
