<?php
require("../bootstrap.php");

use Mpay24\Mpay24Soap;

$Mpay24 = new Mpay24Soap();

$payment = array(
    "amount"   => "100",
    "currency" => "EUR",
);

$additional = array(
    "customerID"      => "customer123",
    "customerName"    => "Jon Doe",
    "confirmationURL" => "http://yourdomain.com/confirmation",
    "order"           => ["description" => "Your description of the Order"], // Optional
);

$type = "PROFILE";

$result = $Mpay24->acceptPayment($type, "123 TID", $payment, $additional);
echo "Status: " . $result->getStatus();
echo "<br>";
echo "ReturnCode: " . $result->getReturnCode();
echo "<br>";
echo "mPAYTID: " . $result->getMpay24Tid();
