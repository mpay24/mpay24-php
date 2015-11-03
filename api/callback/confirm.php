<?php
/**
 * @author            support@mpay24.com
 * @version           $Id: confirm.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource        confirm.php
 * @license           http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
include ("../test.php");
include ("../config/config.php");

foreach($_GET as $key => $value)
  if($key !== 'TID')
    $args[$key] = $value;

$myShop = new MyShop(MERCHANT_ID, SOAP_PASS, TEST_SYSTEM, DEBUG, PROXY_HOST, PROXY_PORT, PROXY_USER, PROXY_PASS, VERIFY_PEER);
$myShop->confirm($_GET['TID'], $args);
?>