<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:29 Uhr
 */

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once ('../mysql.php');

if (isset($_REQUEST['ProductID']))
{

    $ProductID = intval($_REQUEST['ProductID']);

    $sql = 'SELECT UNIX_TIMESTAMP(Prices.Time)*1000 AS "Time", Prices.Price
            FROM Prices
            WHERE ProductID = "'.$ProductID.'"
            AND UNIX_TIMESTAMP(Prices.Time) > UNIX_TIMESTAMP()-365*24*60*60
            ORDER BY Time DESC
            LIMIT 0, 5000';
    $result = $mysqli->query($sql);

    // build rows
    while($row = $result->fetch_assoc())
    {
        $v1["v"] = "Date(" . intval($row["Time"]) . ")";
        $v2["v"] = floatval($row["Price"]);
        $data[] = $v1;
        $data[] = $v2;
        $rows[]["c"] = $data;
        unset($data);
    }

    // build cols
    $col1["label"] = "Date";
    $col1["type"] = "datetime";
    $col2["label"] = "Preis";
    $col2["type"] = "number";
    $cols[] = $col1;
    $cols[] = $col2;


    // return json
    $json["cols"] = $cols;
    $json["rows"] = $rows;
    echo json_encode( $json );

}
