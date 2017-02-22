<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:31 Uhr
 */

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once ('../mysql.php');

$list = Array();

if (isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_REQUEST['email'];

    $sql = "SELECT Tracks.TrackID, Products.ProductID, Products.ProductTitle, Products.ProductUrl, Products.ProductImage, Products.ProductCode, Tracks.PriceStarted, Tracks.PriceAlarm 
            FROM Tracks, Users, Products
            WHERE Users.Email = '" . $email . "'
            AND Products.ProductID = Tracks.ProductID
            AND Users.UserID = Tracks.UserID
            AND IsActive = 1";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        echo json_encode( $list );
        exit;
    } else {
        $error["error"] = "Empty.";
    }
} else {
    $error["error"] = "Wrong parameter.";
}
echo json_encode( $error );