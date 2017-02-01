<?php
require("../bootstrap.php");

use Mpay24\Mpay24Soap;

$Mpay24 = new Mpay24Soap();

$payment = array(
    "amount"         => "100",
    "currency"       => "EUR",
    "manualClearing" => "true",       // Optional: Set to true if you want to do a manual clearing
    "useProfile"     => "true",       // Optional: Set if you want to use the Charge Profile feature
    "profileID"      => "profile123", // Optional: set the profile ID for the customer
);

// All fields are optional, but most of them are highly recommended
$additional = array(
    "customerID"      => "customer123",
    "customerName"    => "Jon Doe",
    "order"           => ["description" => "Your description of the Order"],
    "successURL"      => "http://yourdomain.com/success",
    "errorURL"        => "http://yourdomain.com/error",
    "confirmationURL" => "http://yourdomain.com/confirmation",
    "language"        => "EN",
);

if (isset($_POST["type"])) {
    $type = $_POST["type"];
    switch ($type) {
        case "TOKEN":
            $payment["token"] = $_POST["token"];
            break;
    }

    $result = $Mpay24->acceptPayment($type, "123 TID", $payment, $additional);

    if ($result->getReturnCode() == "REDIRECT") {
        header("Location: " . $result->getLocation());
    } else {
        echo $result->getReturnCode();
    }
}
