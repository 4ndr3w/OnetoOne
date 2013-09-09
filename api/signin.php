<?php
include_once("ApiInclude.php");
$key = $_POST["key"];
$data = $_POST["data"];

$output = Array(API_STATUS => "true", API_INFO => "Functioning normally.");
try{
	if(!$key || !$data)
		throw new Exception("No key and/or data provided.");
	$request = new Api($key);
	$decodedData = json_decode($data, true);
	if(!Helper::exists($decodedData[API_DATA_ID]))
		throw new Exception("Helper does not exist in system");
	$helper = new Helper($decodedData[API_DATA_ID]);
	switch($helper->getStatus()){
		case HELPER_SIGNIN:
		$output[API_INFO] = $helper->getStudentId()." is already logged in.";
		break;
		case HELPER_SIGNOUT:
		$output[API_INFO] = "Logging ".$helper->getStudentId()." in.";
		$helper->signin();
		break;
		default:
		throw new Exception("Helper status error.");
	}
}
catch(Exception $e){
	$output[API_STATUS] = false;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
