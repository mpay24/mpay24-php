<?php
include_once ("../lib/MPAY24.php");
$shop = new MPAY24();

$payment = array(
  "amount" => "100",
  "currency" => "EUR"
);

if(isset($_POST["type"])) {
  $type = $_POST["type"];
  switch($type) {
    case "TOKEN":
      $payment["token"] = $_POST["token"];
      break;
  }
  $result = $shop->acceptPayment($type, "123", $payment);
  if($result->generalResponse->returnCode == "REDIRECT") {
    header('Location: '.$result->location);
  } else {
    echo $result->generalResponse->returnCode;
  }
}
?>
