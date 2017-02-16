<?php
/**
 * Created by PhpStorm.
 * User: Javan
 * Date: 14.02.2017
 * Time: 15:31 Uhr
 */

header('Content-type:application/json;charset=utf-8');

//TODO Read List from Database
$list = Array();
$list[0] = "B00K8D1XD2";
$list[1] = "B00171UT6Q";
$list[2] = "B01BGTG3JA";
$list[3] = "B01N0O6OUI";

echo json_encode( $list );