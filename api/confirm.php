<?php
/**
 * @author            support@mpay24.com
 * @version           $Id: confirm.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource        confirm.php
 * @license           http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
include ("test.php");

foreach($_GET as $key => $value)
  if($key !== 'TID')
    $args[$key] = $value;

$myShop = new MyShop('MerchantID', 'SOAPPassword', TRUE, TRUE);
$myShop->confirm($_GET['TID'], $args);
?>