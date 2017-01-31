<?php
require("../bootstrap.php");
use mPay24\MPAY24;
use mPay24\ORDER;

$mpay24 = new MPAY24();

$mdxi                           = new ORDER();
$mdxi->Order->Tid               = "123 TID";
$mdxi->Order->Price             = "1.00";
$mdxi->Order->URL->Success      = 'http://yourdomain.com/success';
$mdxi->Order->URL->Error        = 'http://yourdomain.com/error';
$mdxi->Order->URL->Confirmation = 'http://yourdomain.com/confirmation';

header('Location: ' . $mpay24->selectPayment($mdxi)->location);
