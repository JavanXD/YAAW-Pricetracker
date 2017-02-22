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

if (isset($_REQUEST['TrackID'])) {
    $TrackID = intval($_REQUEST['TrackID']);

    $mysqli->query('UPDATE Tracks SET IsActive = 0 WHERE TrackID = "'.$TrackID.'"');

}