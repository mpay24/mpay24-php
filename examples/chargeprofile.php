<?php
require("../bootstrap.php");

use Mpay24\Mpay24;

$mpay24 = new Mpay24();

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

$result = $mpay24->payment($type, "123 TID", $payment, $additional);
echo "Status: " . $result->getStatus();
echo "<br>";
echo "ReturnCode: " . $result->getReturnCode();
echo "<br>";
echo "mPAYTID: " . $result->getMpayTid();
