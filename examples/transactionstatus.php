<?php
  include_once ("../lib/MPAY24.php");

  $mpay24 = new MPAY24();

  $params = $mpay24->transactionStatus(null, "TID"); //example with merchant TransaktionID

  echo '<pre>'; print_r($params); echo '</pre>';

?>
