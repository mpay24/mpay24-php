<?php
require("../bootstrap.php");
use mPay24\MPAY24;

$mpay24 = new MPAY24();

$payment = array(
	"amount"     => "100",
	"currency"   => "EUR",
);

$additional = array(
	"customerID"      => "customer123",
	"customerName"    => "Jon Doe",
	"confirmationURL" => "http://yourdomain.com/confirmation",
	"order"           => ["description" => "Your description of the Order"], // Optional
);

$type = "PROFILE";

$result = $mpay24->acceptPayment($type, "123 TID", $payment, $additional);
echo "Status: " . $result->generalResponse->status;
echo "<br>";
echo "ReturnCode: " . $result->generalResponse->returnCode;
echo "<br>";
echo "mPAYTID: " . $result->mpayTID;
