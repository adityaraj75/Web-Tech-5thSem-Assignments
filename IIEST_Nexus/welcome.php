<!DOCTYPE html>


<?php

    require_once('classes/LoginClass.php');
    require_once('classes/variables.php');
    require_once('classes/NexusNav.php');

    if (LoginClass::isLoggedIn())
    {
        header("Location: feed.php");
        exit();
    }

?>

<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Welcome</title>
        <link rel="stylesheet" href="static/css/master.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body class="Back1">
        <?php echo NexusNav::insertNavbar(); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div class="jumbotron">
                        <h1 class='display-5'>Welcome to IIEST Nexus</h1>
                        <hr>
                        <p class='lead'>Check out the cool features of our Web Site!!</p>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="container">
                        <h3>Log In</h3>
                        <form id="LogInForm">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input class="form-control" id="LoginUsername" type="text" name="loginuser" placeholder="Enter Email ID">
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control" id="LoginPassword" type="password" name="loginpass" placeholder="Enter Password">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <button type="button" class="btn btn-primary" name="login" id="loginBtn">Log In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <div class="container">
                        <h3>Sign Up</h3>
                        <form id="SignUpForm">
                            <div class="form-group row">
                                <label class='col-form-label col-sm-3' for="name"><strong>Name</strong></label>
                                <div class="col">
                                    <input class='form-control' type="text" name="name" placeholder="Enter Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class='col-form-label col-sm-3' for="email"><strong>Email</strong></label>
                                <div class="col">
                                    <input class='form-control' type="email" name="email" placeholder="Enter Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class='col-form-label col-sm-3' for="password"><strong>Password</strong></label>
                                <div class="col">
                                    <input class='form-control' type="password" name="password" placeholder="Enter Password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class='col-form-label col-sm-3' for="dob"><strong>Date of Birth</strong></label>
                                <div class="col">
                                    <input class='form-control' type="date" name="dob">
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-success" name="signup">Sign-Up</button>
                                <button type="button" class="btn btn-danger" name="reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="static/js/welcome_script.js"></script>
    </body>
</html>
