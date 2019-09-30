<?php

require_once ('../secrets.php');

// Melde alle Fehler außer E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

//  REQUIREMENTS - cURL and CLI
if ( !function_exists('curl_setopt') OR !function_exists('curl_setopt')  ) {
    echo 'Requires cURL and CLI installations.' ; exit ;
}

function getAmazonPrice($region, $asin) {

	$xml = aws_signed_request($region, array(
		"Operation" => "ItemLookup",
		"ItemId" => $asin,
		"IncludeReviewsSummary" => False,
		"ResponseGroup" => "Medium,OfferSummary",
	));

	$item = $xml->Items->Item;
	$title = htmlentities((string) $item->ItemAttributes->Title);
	$url = htmlentities((string) $item->DetailPageURL);
	$image = htmlentities((string) $item->MediumImage->URL);
	$price = htmlentities((string) $item->OfferSummary->LowestNewPrice->Amount);
	$code = htmlentities((string) $item->OfferSummary->LowestNewPrice->CurrencyCode);
	$qty = htmlentities((string) $item->OfferSummary->TotalNew);

	if ($qty !== "0") {
		$response = array(
			"code" => $code,
			"price" => number_format((float) ($price / 100), 2, '.', ''),
			"image" => $image,
			"url" => $url,
			"title" => $title
		);
	}

	return $response;
}

function getPage($url) {

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
	$html = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    /*$information = curl_getinfo($curl);
    echo "<pre>";
    var_dump($information);
    echo "</pre>";*/
	curl_close($curl);

	if($httpcode != "200") {
        return false; // error in API request
    }

    return $html;

}

function aws_signed_request($region, $params) {
    global $associate_tag, $public_key, $private_key;

    $method = "GET";
	$host = "ecs.amazonaws." . $region;
	$host = "webservices.amazon." . $region;
	$uri = "/onca/xml";

	$params["Service"] = "AWSECommerceService";
	$params["AssociateTag"] = $associate_tag;
	$params["AWSAccessKeyId"] = $public_key;
	$params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	$params["Version"] = "2011-08-01";

	ksort($params);

	$canonicalized_query = array();
	foreach ($params as $param => $value) {
		$param = str_replace("%7E", "~", rawurlencode($param));
		$value = str_replace("%7E", "~", rawurlencode($value));
		$canonicalized_query[] = $param . "=" . $value;
	}

	$canonicalized_query = implode("&", $canonicalized_query);

	$string_to_sign = $method . "\n" . $host . "\n" . $uri . "\n" . $canonicalized_query;
	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));
	$signature = str_replace("%7E", "~", rawurlencode($signature));

	$request = "http://" . $host . $uri . "?" . $canonicalized_query . "&Signature=" . $signature;
	$response = getPage($request);
    if($response == false){
        return False;// error in API request
    }
	$pxml = @simplexml_load_string($response);
	if ($pxml === False) {
		return False;// no xml
	} else {
		return $pxml;
	}
}

?>