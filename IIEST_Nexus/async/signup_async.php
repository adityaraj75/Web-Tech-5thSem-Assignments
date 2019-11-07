<?php
    // PHP File for asynchronous requests of Signup

    require_once('../classes/DataBase.php');
    require_once('../classes/LoginClass.php');
    require_once('../classes/User.php');
    require_once('../classes/variables.php');


    if (isset($_POST['signup']))
    {
        $name       = $_POST['name'];
        $email      = $_POST['email'];
        $password   = $_POST['password'];
        $dob        = date_create($_POST['dob']);
        $dob        = date_format($dob,"Y/m/d");

        $query_result = DataBase::query('SELECT email FROM '.DataBase::$user_table_name.
                                        ' WHERE email=:email',
                                        array(':email'=>$email)
                                    );
        if ($query_result['executed']===false)
        {
            // echo "ERROR: Could not able to execute SQL<br>";
            // print_r($query_result['errorInfo']);
            $query_result['errorMessage']="Not Able to Execute SQL!";
            echo json_encode($query_result);
            exit();
        }

        if (count($query_result['data'])===0)
        {
            if (preg_match("/^[a-zA-Z'. -]+$/", $name))
            {
                if ((strlen($password)) >= 6 && (strlen($password)<=60))
                {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        // Insert New Records into DataBase
                        DataBase::query('INSERT INTO '.DataBase::$user_table_name.
                                        ' VALUES (:name,:email,:dob,DEFAULT,:password,:profilePicPath)',
                                        array(':name'=>$name,
                                              ':email'=>$email,
                                              ':dob'=>$dob,
                                              ':password'=>password_hash($password, PASSWORD_BCRYPT),
                                              ':profilePicPath'=>'media/profile-placeholder.jpg')
                                    );

                        // Get userid
                        $user_id = DataBase::query('SELECT id FROM '.DataBase::$user_table_name.
                        ' WHERE email=:email',
                        array(':email'=>$email)
                        )['data'][0]['id'];


                        // Add themselves as followers of their own
                        $result = DataBase::query('INSERT INTO '.DataBase::$follow_table_name.' (userid,followerid)'.
                                                  ' VALUES (:userid, :followerid)',
                                                  array(':userid'=>$user_id,
                                                        ':followerid'=>$user_id)
                                                );

                        // Log the new user in
                        $crypto_strong = True;
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));

                        DataBase::query('INSERT INTO '.DataBase::$token_table_name.
                                        ' VALUES (DEFAULT, :token, :userid)',
                                        array(':token'=>sha1($token),
                                              ':userid'=>$user_id)
                                  );

                        setcookie("SNID", $token, time() + 60*60*24*7, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                        setcookie("SNID_", $token, time() + 60*60*24*3, NetworkVariables::$cookie_path, NULL, NULL, TRUE);
                        $result['executed']=true;
                        $result['message']="Successfully Signed Up and logged In!";
                        $result['redirectLink']=NetworkVariables::$home_path.'feed.php';
                        echo json_encode($result);
                        exit();
                    }
                    else
                    {
                        $result['executed']=false;
                        $result['errorMessage']="Invalid email!";
                        echo json_encode($result);
                        exit();
                    }
                }
                else
                {
                    $result['executed']=false;
                    $result['errorMessage']="Password should be 6 to 60 characters long!";
                    echo json_encode($result);
                    exit();
                }
            }
            else
            {
                $result['executed']=false;
                $result['errorMessage']="Improper Name!";
                echo json_encode($result);
                exit();
            }
        }
        else
        {
            $result['executed']=false;
            $result['errorMessage']="Email already registered!";
            echo json_encode($result);
            exit();
        }
    }

?>
