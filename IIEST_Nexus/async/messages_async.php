<?php
    // PHP File for asynchronous requests of Login

    require_once('../classes/DataBase.php');
    require_once('../classes/LoginClass.php');
    require_once('../classes/User.php');
    require_once('../classes/variables.php');

    if(isset($_POST['login']))
    {
        $email              = $_POST['loginuser'];
        $password           = $_POST['loginpass'];

        $query_result = DataBase::query('SELECT email FROM '.DataBase::$user_table_name.
                                        ' WHERE email=:email',
                                        array(':email'=>$email));
        if ($query_result['executed']===false)
        {
            // echo "ERROR: Not able to execute SQL<br>";
            $query_result['errorMessage']="Not Able to Execute SQL!";
            echo json_encode($query_result);
            exit();
        }


        if (count($query_result["data"])===1)
        {
            if (password_verify($password, DataBase::query('SELECT password FROM '.DataBase::$user_table_name.' WHERE email=:email', array(':email'=>$email))["data"][0]['password']))
            {
                $crypto_strong = True;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
                $user_id = DataBase::query('SELECT id FROM '.DataBase::$user_table_name.' WHERE email=:email',
                                            array(':email'=>$email))['data'][0]['id'];
                DataBase::query('INSERT INTO '.DataBase::$token_table_name.' VALUES (DEFAULT, :token, :userid)',
                                array(':token'=>sha1($token), ':userid'=>$user_id));
                setcookie("SNID", $token, time() + 60*60*24*7, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                setcookie("SNID_", $token, time() + 60*60*24*3, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                $result['executed']=true;
                $result['message']="Successfully Logged In!";
                $result['redirectLink']=NetworkVariables::$home_path.'feed.php';
                echo json_encode($result);
                exit();
            }
            else
            {
                $result['executed']=false;
                $result['errorMessage']="Incorrect Password!";
                echo json_encode($result);
                exit();
            }
        }
        else
        {
            $result['executed']=false;
            $result['errorMessage']="User not registered!";
            echo json_encode($result);
            exit();
        }
    }

?>
