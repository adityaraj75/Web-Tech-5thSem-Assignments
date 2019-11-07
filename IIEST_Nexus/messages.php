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

    $current_user = new User($logged_in_id);

?>

<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $current_user->getName(); ?> | Messages</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="static/css/master.css">
        <link rel="stylesheet" href="static/css/messages_master.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    </head>
    <body>
        <!-- Navbar Begins-->
        <?php echo NexusNav::insertNavbar(); ?>
        <!-- Navbar Ends-->

        <?php if (isset($_POST['convoid'])) {
            $messageUser=new User($_POST['convoid']);
            if($messageUser){ ?>
                <input type="number" id="convoUserID" value="<?php echo $messageUser->getID(); ?>" hidden>
                <input type="text" id="convoUserName" value="<?php echo $messageUser->getName(); ?>" hidden>
            <?php }
            echo '<script>'.
                    'window.onload = function(){'.
                        'history.replaceState("", "", "messages.php");'.
                    '}'.
            '</script>';
        } ?>
        <div class="container-fluid messagingScreen">
            <div class="row messagingScreenRow">
                <!-- Chat List Begins -->
                <div class="col-sm-4 userList">

                    <!-- <div class="chat-search-box">
                        <div class="input-group">
                            <input class="form-control" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-info">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div> -->

                    <!-- List of Users with messages -->
                    <ul class="chatUsersList">
                    </ul>
                </div>
                <!-- Chat List Ends -->


                <!-- Chat Box Begins -->
                <div class="col-sm-8 chatBox">

                </div>
                <!-- Chat Box Ends -->

            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="static/js/messages_script.js"></script>
    </body>
</html>
