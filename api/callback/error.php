<?php
/**
 * @author              support@mpay24.com
 * @version             $Id: error.php 6231 2015-03-13 16:29:56Z anna $
 * @filesource          error.php
 * @license             http://ec.europa.eu/idabc/eupl.html EUPL, Version 1.1
 */
echo "<!DOCTYPE html PUBLIC \"HTML\">
<html>
<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">
</head>
<body>";

foreach($_REQUEST as $key => $value)
  echo "$key = " . utf8_encode(urldecode($value)) . "<br/>";

echo "
<a href='../index.html'>Order again!</a>
</body>
</html>";
?>