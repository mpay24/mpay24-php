<?php
  require("../bootstrap.php");
  use mPay24\MPAY24;

  $shop = new MPAY24();

  $mdxi = new ORDER();
  $mdxi->Order->Tid = "123";
  $mdxi->Order->Price = "1.00";

  header('Location: '.$shop->selectPayment($mdxi)->location);
?>
