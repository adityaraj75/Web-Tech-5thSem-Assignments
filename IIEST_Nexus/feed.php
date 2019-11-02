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
        <title><?php echo $current_user->getName(); ?> | Feed</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="static/css/master.css">
        <link rel="stylesheet" href="static/css/feed_master.css">
    </head>
    <body>
        <!-- Navbar Begins-->
        <?php echo NexusNav::insertNavbar(); ?>
        <!-- Navbar Ends-->

        <div class="container-fluid">
            <div class="row">
                <!-- Left Column Begin-->
                <div class="col-sm-3" id="leftColumn">
                    <div style="width:100%;" class="btn-group btn-group-toggle" data-toggle="buttons" id="searchOptionDiv">
                        <label style="width:50%;" class="btn btn-light active" id="searchByPeopleLabel">
                            <input type="radio" name="searchPeopleOption" id="searchByPeople" value="byName" autocomplete="off" checked> Search By Name
                        </label>
                        <label style="width:50%;" class="btn btn-light" id="searchBySkillLabel">
                            <input type="radio" name="searchPeopleOption" id="searchBySkill" value="bySkill" autocomplete="off"> Search By Skill
                        </label>
                    </div>
                    <p></p>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="search-box">
                                <input class="form-control" type="text" size="30" autocomplete="off" name="peopleSearch" placeholder="Enter Name">
                                <div class="result" id="peopleLiveSearch"></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="searchPeopleBtn">Find</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9">
                            <div class="peopleSearchResult">

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ghost Left Col Begin-->
                <div class="col-sm-3" style="z-index: -1;">
                </div>
                <!-- Ghost Left Col End-->

                <!-- Left Column End -->






                <!-- Middle Column Begin-->
                <div class="col-sm-6">

                    <!-- Posting Begin-->
                    <div class="container">
                        <div class="card">
                            <div class="card-header">
                                <div class="form-group">
                                    <textarea class="form-control" name="postbody" id="postContent" placeholder="Enter your thoughts here"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="form-control btn btn-primary" name="createPost" id="postCreateBtn">Share</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Posting End -->

                    <!-- Post List Begin -->
                    <div class="container">
                        <div class="postBox">

                        </div>
                    </div>
                    <!-- Post List End -->

                </div>
                <!-- Middle Column End -->




                <!-- Right Column Begin -->
                <div class="col-sm-3" id="rightColumn">
                    <p>Do something else here losers</p>
                </div>
                <!-- Right Column End -->


            </div>
        </div>

        <!-- <div class="container">
            <div class="jumbotron">
                <p class="lead">Welcome to your feed <?php //echo $current_user->getName(); ?></p>
            </div>
        </div> -->



        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="static/js/feed_script.js"></script>
    </body>
</html>
