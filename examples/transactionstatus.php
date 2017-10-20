<?php
require("../bootstrap.php");

use Mpay24\Mpay24;

$mpay24 = new Mpay24();

$params = $mpay24->paymentStatusByTID("TID"); //example with merchant TransaktionID

echo '<pre>'; print_r($params); echo '</pre>';

?>
