<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 15.02.2017
 * Time: 16:37 Uhr
 */


$host = "localhost";
$user = "my_user";
$password = "my_password";
$database = "my_db";

$mysqli = mysqli_connect($host, $user, $password, $database);

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