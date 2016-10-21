<?php
include_once ("../lib/MPAY24.php");
$myShop = new MPAY24();
?>
<iframe src="<?php echo $myShop->payWithToken("CC")->getPaymentResponse()->location; ?>"></iframe>
