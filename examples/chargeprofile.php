<?php
  include_once ("../lib/MPAY24.php");

  $shop = new MPAY24();

  $payment = array(
    "amount" => "100",
    "currency" => "EUR"
  );

  $additional = array(
    "customerID" => "customer123",
    "confirmationURL" => "http://yourdomain.com/confirmation"
  );


  $type="PROFILE";

  $result = $shop->acceptPayment($type, "123", $payment, $additional);
  echo "Status: ".$result->generalResponse->status;
  echo "<br>";
  echo "ReturnCode: ".$result->generalResponse->returnCode;
  echo "<br>";
  echo "mPAYTID: ".$result->mpayTID;
?>
