# mpay24-php

[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)]()

Offical PHP SDK for SOAP Bindings

## Documentation

A short demo implementation guide is available at https://docs.mpay24.com/docs/get-started</br>
Documentation is available at https://docs.mpay24.com/docs.

## Configuration

You can use the config.php file in the root directory

You also can handover the parameters while crating the MPAY24 Object

```php
require_once("../bootstrap.php");
use mPay24\MPAY24;

$mpay24 = new MPAY24('9****', '*********');

```

If you want to have a more flexible approach you can create a configuration object.
Here you can either handover the parameters like you do it with the MPAY24 Object
and/or you use the methods coming with the configuration object

```php
require_once("../bootstrap.php");
use mPay24\MPay24Config;

$config = new MPay24Config('9****', '*********');
$mpay24 = new MPAY24($config);

```

You have the possibility to change any value as needed:

```php
$config->setMerchantID('9****');
$config->setSoapPassword('*******');
$config->useTestSystem(true);   // true => Use the Test System [DEFAULT], false => use the Live System
$config->setDebug(true);        // true => Debug Mode On [DEFAULT], false => Debug Mode Off
```

For proxy configuration (only if needed)

```php
$config->setProxyHost('example.com');
$config->setProxyPort(0815);            // Must be 4 digits
$config->setProxyUser('proxyuser');
$config->setProxyPass('*******');
```

Configure the for Flex Link usage:

```php
$config->setSPID('spid');
$config->setFlexLinkPassword('*******');
$config->useFlexLinkTestSystem(true);   // true => Use the Flex Link Test System [DEFAULT], false => use Flex Link Live System
```

Logs are written into `./logs` per default but you can change it in the config.php or
with the configuration object if used

```php
$config->setLogPath('/absolute/path/to/log/dir');
```

Other configuration options:
```php
$config->setVerifyPeer(true);           // true => Verify the Peer  [DEFAULT], false => stop cURL from verifying the peer's certificate
$config->setEnableCurlLog(false);       // false => we do not log Curl comunicatio [DEFAULT], true => we log it to a seperat Log file
$config->setLogFile('file_name.log');   // default is mpay24.log
$config->setCurlLogFile('curl.log');    // default is curllog.log
```

## SDK Overview

First it is necessary to include and initialize the library with username and password:
```php
require_once("../bootstrap.php");
use mPay24\MPAY24;

$mpay24 = new MPAY24('9****', '*********');
```

#### Create a token for seamless creditcard payments

```php
$tokenizer = $mpay24->createPaymentToken("CC")->getPaymentResponse();
```

#### Create a payment

Creditcard payment with a token
```php
$payment = array(
  "amount" => "100",
  "currency" => "EUR",
  "token" => ""
);
$result = $mpay24->acceptPayment("TOKEN", "123", $payment);
```
Paypal payment
```php
$payment = array(
  "amount" => "100",
  "currency" => "EUR"
);
$result = $mpay24->acceptPayment("PAYPAL", "123", $payment);
```

#### Create a checkout

Initialize a minimum paypage
```php
$mdxi = new ORDER();
$mdxi->Order->Tid = "123";
$mdxi->Order->Price = "1.00";

$checkoutURL = $mpay24->selectPayment($mdxi)->location; // redirect location to the payment page

header('Location: '.$checkoutURL);
```

#### Get current transaction status

```php
$mpay24->transactionStatus(12345); // with mpaytid
$mpay24->transactionStatus(null, "123 TID"); // with tid
```
### Prerequisites

In order for the mPAY24 PHP SDK to work, your installation will have to meet the following prerequisites:

* [PHP >= 5.3.3](http://www.php.net/)
* [cURL](http://at2.php.net/manual/de/book.curl.php)
* [DOM](http://at2.php.net/manual/de/book.dom.php)
* [Mcrypt](http://at2.php.net/manual/en/mcrypt)

Please refer to http://www.php.net/phpinfo or consult your systems administrator in order to find out if your system fulfills the prerequisites.
