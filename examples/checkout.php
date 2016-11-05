<?php
  include_once ("../lib/MPAY24.php");
  
  $shop = new MPAY24();

  $mdxi = new ORDER();
  $mdxi->Order->Tid = "123";
  $mdxi->Order->Price = "1.00";

  header('Location: '.$shop->selectPayment($mdxi)->location);
?>
