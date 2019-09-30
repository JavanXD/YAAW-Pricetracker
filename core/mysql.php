<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 15.02.2017
 * Time: 16:37 Uhr
 */

include_once ('./secrets.php');

$mysqli = mysqli_connect($GLOBALS["host"], $GLOBALS["user"], $GLOBALS["password"], $GLOBALS["database"], 3306);

// Check connection
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

function close_mysql($mysqli)
{
    $mysqli->close();
}
register_shutdown_function('close_mysql', $mysqli);

?>