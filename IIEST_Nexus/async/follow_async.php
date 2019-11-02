<?php
    // PHP File for asynchronous requests of Follows, if logged in.

    require_once('../classes/DataBase.php');
    require_once('../classes/LoginClass.php');
    require_once('../classes/User.php');
    require_once('../classes/variables.php');

    $logged_in_id = LoginClass::isLoggedIn();

    if (! $logged_in_id)
    {
        // Someone is trying to access this php file and inspect its content
        // through direct URL.
        header("Location: index.php");
        exit();
    }

    $current_user = new User($logged_in_id);

    // To follow someone:
    if( isset($_POST['follow']) ){
        $to_follow_user=new User($_POST['follow']);

        if(!$to_follow_user->isReal()){
            $result['executed']=false;
            $result['errorMessage']='User does not Exist!';
            echo json_encode($result);
            exit();
        }

        $result = DataBase::query('INSERT INTO '.DataBase::$follow_table_name.' (userid,followerid)'.
                                  ' VALUES (:userid, :followerid)',
                                  array(':userid'=>$to_follow_user->getID(),
                                        ':followerid'=>$current_user->getID())
                                );
        $result_json=json_encode($result);
        echo $result_json;
        exit();
    }


    // To Unfollow someone:
    if( isset($_POST['unfollow']) ){
        $to_unfollow_user=new User($_POST['unfollow']);

        if(!$to_unfollow_user->isReal()){
            $result['executed']=false;
            $result['errorMessage']='User does not Exist!';
            echo json_encode($result);
            exit();
        }

        $result = DataBase::query('DELETE FROM '.DataBase::$follow_table_name.
                                  ' WHERE userid = :userid'.
                                    ' AND followerid = :followerid',
                                  array(':userid'=>$to_unfollow_user->getID(),
                                        ':followerid'=>$current_user->getID())
                                );
        $result_json=json_encode($result);
        echo $result_json;
        exit();
    }


?>
