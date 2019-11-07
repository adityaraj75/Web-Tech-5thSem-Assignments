<?php

require_once('LoginClass.php');
require_once('DataBase.php');
require_once('variables.php');

class NexusNav
{
    private static $logged_in_part1='
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto mt-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="meDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
    private static $logged_in_part2=
                        '</a>
                        <div class="dropdown-menu" aria-labelledby="meDropdown">
                            <a class="dropdown-item" href="profile.php">Profile</a>
                            <a class="dropdown-item" href="messages.php">Chats</a>
                        </div>
                    </li>
                </ul>
                <form class="form-inline my-0 mx-3" method="POST">
                    <input class="btn btn-sm btn-outline-danger" type="submit" name="logout" value="Logout" formaction="logout.php">
                </form>
            </div>
            ';

    public static function insertNavbar()
    {
        $retString='<nav>';
        $retString='<nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
                        <a class="navbar-brand" href="'.NetworkVariables::$home_path.'">IIEST Nexus</a>';
        $id = LoginClass::isLoggedIn();
        if($id)
        {
            $retString=$retString.self::$logged_in_part1;
            $query_result = DataBase::query('SELECT profilepic FROM '.DataBase::$user_table_name.
                                            ' WHERE id=:id',
                                            array(':id'=>$id));
            $retString=$retString.'<img src="'.$query_result['data'][0]['profilepic'].'" width="30px" height="30px" style="border-radius:50%;" alt="Me">';
            $retString=$retString.self::$logged_in_part2;
        }
        $retString=$retString.'</nav>';
        return $retString;
    }

}

?>
