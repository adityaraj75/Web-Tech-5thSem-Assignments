<?php

require_once("../../classes/LoginClass.php");
require_once("../../classes/variables.php");

if (LoginClass::isLoggedIn())
{
    header("Location: ".NetworkVariables::$home_path."feed.php");
    exit();
}
else
{
    header("Location: ".NetworkVariables::$home_path."welcome.php");
    exit();
}

?>
