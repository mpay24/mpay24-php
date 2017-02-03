<?php
require("../bootstrap.php");

use Mpay24\Mpay24Order;
use Mpay24\Mpay24;

$mpay24 = new Mpay24();

$mdxi                           = new Mpay24Order();
$mdxi->Order->Tid               = "123 TID";
$mdxi->Order->Price             = "1.00";
$mdxi->Order->URL->Success      = "http://yourdomain.com/success";
$mdxi->Order->URL->Error        = "http://yourdomain.com/error";
$mdxi->Order->URL->Confirmation = "http://yourdomain.com/confirmation";

header("Location: " . $mpay24->paymentPage($mdxi)->getLocation());
