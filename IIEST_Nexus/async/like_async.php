<?php
    // PHP File for asynchronous requests of posts, if logged in.

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

    // For getting no of likes
    if(isset($_POST['getLikes'])){
        $result = DataBase::query('SELECT likes FROM '.DataBase::$posts_table_name.
                                  ' WHERE id = '.$_POST['getLikes']);
        if ($result['executed']===false){
            echo "ERROR: Not able to execute SQL<br>";
            exit();
        }
        $result['data']['alreadydone'] = 0;
        $tempresult=DataBase::query('SELECT count(likedby) as temp from '.DataBase::$like_table_name.' where postid = '.$_POST['getLikes'].' and likedby = '.$current_user->getID());
        if($tempresult['executed'])
        {
            $tempresult=$tempresult['data'];
            if($tempresult[0][0])
                $result['data']['alreadydone'] = 1;
        }
        $result=json_encode($result['data']);
        echo $result;
    }

    // For Liking Post
    if(isset($_POST['likePost'])){
        $postid=$_POST['likePost'];
        $result = DataBase::query('INSERT INTO '.DataBase::$like_table_name.
                                 ' VALUES (:postid,:likedby)',
                                 array(':postid'=>$postid,
                                       ':likedby'=>$current_user->getID())
                               );

        if ($result['executed']===false){
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result);
            exit();
        }

        $result = DataBase::query('UPDATE '.DataBase::$posts_table_name.
                                 ' SET likes=likes+1'.
                                 ' WHERE id='.$postid
                             );

        if ($result['executed']===false){
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result);
            exit();
        }

        $result = json_encode($result);
        echo $result;
    }

    // For unliking Post
    if(isset($_POST['unlikePost'])){
        $postid=$_POST['unlikePost'];
        $result = DataBase::query('DELETE FROM '.DataBase::$like_table_name.
                                 ' WHERE likedby = '.$current_user->getID().
                                    ' AND postid = '.$postid);

        if ($result['executed']===false){
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result);
            exit();
        }

        $result = DataBase::query('UPDATE '.DataBase::$posts_table_name.
                                 ' SET likes=likes-1'.
                                 ' WHERE id='.$postid
                             );

        if ($result['executed']===false){
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result);
            exit();
        }

        $result=json_encode($result);
        echo $result;
    }


?>
