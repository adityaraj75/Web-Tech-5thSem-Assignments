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


    // To create a new post
    if(isset($_POST['createpost']))
    {
        $postbody = $_POST['postbody'];
        if (strlen($postbody) < 1)
        {
            die('Incorrect length!');
        }

        $result=DataBase::query('INSERT INTO '.DataBase::$posts_table_name.
                                ' VALUES (DEFAULT, :postbody, NOW(), :userid, 0)',
                                array(':postbody'=>$postbody,
                                        ':userid'=>$current_user->getId()
                                    )
                            );

        if ($result['executed']===false)
        {
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result);
            echo $result_json;
        }
    }


    // To show all posts ordered by time desc
    if(isset($_POST['showAllPost']))
    {
        $result = DataBase::query('SELECT '.DataBase::$posts_table_name.'.id,body,posted_at,name as username,user_id as userid,likes,profilepic'.
                                    ' FROM '.DataBase::$posts_table_name.','.DataBase::$user_table_name.
                                    ' WHERE user_id='.DataBase::$user_table_name.'.id'.
                                    ' ORDER BY '.DataBase::$posts_table_name.'.id DESC');
        if ($result['executed']===false)
        {
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result);
            echo $result_json;
        }
    }


    // To show posts of people who the current user follows ordered by time desc
    if(isset($_POST['showFollowingPost']))
    {
        $result = DataBase::query('SELECT '.DataBase::$posts_table_name.'.id as postid,body,posted_at,name AS username,user_id AS userid,likes,profilepic'.
                                    ' FROM '.DataBase::$posts_table_name.','.DataBase::$user_table_name.
                                    ' WHERE user_id='.DataBase::$user_table_name.'.id'.
                                        ' AND user_id IN (SELECT userid'.
                                                        ' FROM '.DataBase::$follow_table_name.
                                                        ' WHERE followerid = :currentuserid )'.
                                    ' ORDER BY '.DataBase::$posts_table_name.'.id DESC',
                                    array(':currentuserid'=>$current_user->getID())
                                );
        if ($result['executed']===false)
        {
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result);
            echo $result_json;
        }
    }



?>
