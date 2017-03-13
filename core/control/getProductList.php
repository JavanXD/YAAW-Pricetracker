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
    $email = $mysqli->real_escape_string($_REQUEST['email']);

    $sql = "SELECT Tracks.TrackID, Tracks.IsFavorite, Products.ProductID, Products.ProductTitle, Products.ProductUrl, Products.ProductImage, Products.ProductCode, Tracks.PriceStarted, Tracks.PriceAlarm 
            FROM Tracks, Users, Products
            WHERE Users.Email = '" . $email . "'
            AND Products.ProductID = Tracks.ProductID
            AND Users.UserID = Tracks.UserID
            AND IsActive = 1
            ORDER BY Tracks.IsFavorite DESC, Tracks.Created ASC";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {

            // hol zu dem Produkt noch des Maxima und das Minima
            $sql = 'SELECT ROUND(MIN(Price),2) AS "Min", ROUND(MAX(Price),2) AS "Max", ROUND(AVG(Price),2) AS "AVG", Price AS "Newest", Time
            FROM Prices
            WHERE ProductID = "' . $row['ProductID'] . '"
            ORDER BY Time DESC
            LIMIT 0,1';
            $result_product = $mysqli->query($sql);
            $row_product = $result_product->fetch_assoc();

            // erweitere Array um Maxima und Minima
            $row["Min"] = $row_product["Min"];
            $row["Max"] = $row_product["Max"];
            $row["AVG"] = $row_product["AVG"];

            // füge erweitertes Produkt der Liste hinzu
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