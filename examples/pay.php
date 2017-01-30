<?php
  require("../bootstrap.php");
  use mPay24\MPAY24;
  
  $mpay24 = new MPAY24();

  $payment = array(
    "amount" => "100",
    "currency" => "EUR"
  );

  $additional = array(
    "customerID" => "customer123",
    "successURL" => "http://yourdomain.com/success",
    "errorURL" => "http://yourdomain.com/error",
    "confirmationURL" => "http://yourdomain.com/confirmation",
    "cancelURL" => "http://yourdomain.com/cancel"
  );

  if(isset($_POST["type"])) {
    $type = $_POST["type"];
    switch($type) {
      case "TOKEN":
        $payment["token"] = $_POST["token"];
        break;
    }

    $result = $mpay24->acceptPayment($type, "123", $payment, $additional);

    if($result->generalResponse->returnCode == "REDIRECT") {
      header('Location: '.$result->location);
    } else {
      echo $result->generalResponse->returnCode;
    }
  }
