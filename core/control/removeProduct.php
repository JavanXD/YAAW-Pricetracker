<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:29 Uhr
 */

header( 'Content-type: text/html; charset=utf-8' );
header('Access-Control-Allow-Origin: *');

require_once ('../mysql.php');

if (isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) && isset($_REQUEST['TrackID'])) {

    $email = $mysqli->real_escape_string($_REQUEST['email']);
    $TrackID = intval($_REQUEST['TrackID']);

    $mysqli->query('UPDATE Tracks T JOIN Users U ON (U.UserID = T.UserID)
                    SET T.IsActive = 0
                    WHERE U.Email = "' . $email. '"
                    AND T.TrackID = "' . $TrackID. '"');

    if ($mysqli->affected_rows == 1)
    {
        $json["success"] = "true";
    }else{
        $json["success"] = "false";
    }

    // Send Result
    if (isset($_REQUEST['directAccess'])) {
        header("Location: https://www.yaaw.de/list.html?email=" . urlencode($email));
        exit;
    } else {
        echo json_encode( $json );
    }
}