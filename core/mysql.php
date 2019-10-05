<?php

require_once ('secrets.php');

$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, 3306);

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

