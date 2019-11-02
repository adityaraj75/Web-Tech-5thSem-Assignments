<!DOCTYPE html>


<?php
require_once('classes/LoginClass.php');
require_once('classes/User.php');
require_once('classes/variables.php');
require_once('classes/NexusNav.php');
require_once('classes/DataBase.php');


$logged_in_id = LoginClass::isLoggedIn();

if (! $logged_in_id)
{
    header("Location: welcome.php");
    exit();
}

$logged_in_as = new User(LoginClass::isLoggedIn());


if (isset($_GET['mid']))
{

        $res = DataBase::query('SELECT * FROM '.DataBase::$message_table_name.' WHERE id=:mid', array(':mid'=>$_GET['mid']));
        if(!$res['executed'])
            die('Unexpected error occured. Contact us!');
        $message = $res['data'][0];
        echo '<h1>View Message</h1>';
        echo htmlspecialchars($message['body']);
        echo '<hr />';
        if ($message['sender'] == $logged_in_as->getID())
        {
                $id = $message['receiver'];
        }
        else
        {
                $id = $message['sender'];
        }
        DataBase::query('UPDATE '.DataBase::$message_table_name.' SET seen = 1 WHERE id=:mid', array (':mid'=>$_GET['mid']));
        ?>

        <form action="sendmessage.php?recid=<?php echo $id; ?>" method="post">
                <textarea name="body" rows="8" cols="80"></textarea>
                <input type="submit" name="send" value="Reply">
        </form>
        <?php
}
else
{
        ?>

    <html lang="en" dir="ltr">
        <head>
            <meta charset="utf-8">
            <title>
            </title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <link rel="stylesheet" href="static/css/master.css">
        </head>
        <body>

            <?php echo NexusNav::insertNavbar(); ?>
            <h1>My Messages</h1>
            <?php
                $res = DataBase::query('SELECT '.DataBase::$message_table_name.'.*, '.DataBase::$user_table_name.'.name FROM '.DataBase::$message_table_name.',
                                        '.DataBase::$user_table_name.' WHERE receiver=:receiver OR sender=:sender AND '.DataBase::$user_table_name.'.id = '.DataBase::$message_table_name.
                                        '.sender', array(':receiver'=>$logged_in_as->getID(), ':sender'=>$logged_in_as->getID()));
                if(!$res['executed'])
                {
                    die("Unexpected error occured. Contact us!");
                }
                $messages = $res['data'];
                $short=0;
                foreach ($messages as $message)
                {
                        if (strlen($message['body']) > 10)
                        {
                                $m = substr($message['body'], 0, 10)." ...";
                        }
                        else
                        {
                                $m = $message['body'];
                        }
                        if ($message['seen'] == 0)
                        {
                                echo "<a href='mymessages.php?mid=".$message['id']."'><strong>".$m."</strong></a> sent by ".$message['name'].'<hr />';
                        }
                        else
                        {
                                echo "<a href='mymessages.php?mid=".$message['id']."'>".$m."</a> sent by ".$message['name'].'<hr />';
                        }
                }
}
            ?>
            <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
            <script src="static/js/skills_script.js"></script>

        </body>
    </html>
