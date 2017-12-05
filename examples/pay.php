<?php
require("../bootstrap.php");

use Mpay24\Mpay24;

$mpay24 = new Mpay24();

$payment = array(
    "amount"         => "100",
    "currency"       => "EUR",
    "manualClearing" => "false",       // Optional: set to true if you want to do a manual clearing
    "useProfile"     => "false",       // Optional: set if you want to create a profile
);

// All fields are optional, but most of them are highly recommended
$additional = array(
    "customerID"      => "customer123", // Required if useProfile is true
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

    $result = $mpay24->payment($type, "123 TID", $payment, $additional);

    if ($result->getReturnCode() == "REDIRECT") {
        header("Location: " . $result->getLocation());
    } else {
        echo $result->getReturnCode();
    }
}
