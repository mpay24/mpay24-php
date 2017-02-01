# mpay24-php

[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)]()

Offical PHP SDK for SOAP Bindings

## Documentation

A short demo implementation guide is available at https://docs.mpay24.com/docs/get-started</br>
Documentation is available at https://docs.mpay24.com/docs.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require mpay24/mpay24-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require __DIR__ . '/vendor/autoload.php';
```

## Manual Installation

If you do not want to use Composer, you can download the [latest release](https://github.com/mpay24/mpay24-php/releases). Then, to use the bindings, include the `bootstrap.php` file.

```php
require_once('bootstrap.php');
```

## SDK overview

#### Configuration

You can use the config.php file in the root directory

You also can handover the parameters while crating the mpay24 Object

```php
require_once("../bootstrap.php");
use Mpay24\Mpay24Soap;

$mpay24 = new Mpay24Soap('9****', '*********');

```

If you want to have a more flexible approach you can create a [configuration object](https://github.com/mpay24/mpay24-php/wiki/Configuring-the-php-sdk).

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
  "token" => $_POST['token']
);
$result = $mpay24->acceptPayment("TOKEN", "123 TID", $payment);
```
Paypal payment
```php
$payment = array(
  "amount" => "100",
  "currency" => "EUR"
);
$result = $mpay24->acceptPayment("PAYPAL", "123 TID", $payment);
```

#### Create a checkout

Initialize a minimum paypage
```php
$mdxi = new ORDER();
$mdxi->Order->Tid = "123";
$mdxi->Order->Price = "1.00";
$mdxi->Order->URL->Success      = 'http://yourpage.com/success';
$mdxi->Order->URL->Error        = 'http://yourpage.com/error';
$mdxi->Order->URL->Confirmation = 'http://yourpage.com/confirmation';

$checkoutURL = $mpay24->selectPayment($mdxi)->location; // redirect location to the payment page

header('Location: '.$checkoutURL);
```

#### Get current transaction status
With the TID number that we received by the merchant request

*If you don't have unique TID you will only get the last transaction with this number*
```php
$Mpay24->transactionStatusByTID("123 TID");
```

With the unique mPAYTID number that we send back in the response messages
```php
$mpay24->transactionStatusByMpay24Tid("12345");
```

### Prerequisites

In order for the Mpay24 PHP SDK to work, your installation will have to meet the following prerequisites:

* [PHP >= 5.3.3](http://www.php.net/)
* [cURL](http://at2.php.net/manual/de/book.curl.php)
* [DOM](http://at2.php.net/manual/de/book.dom.php)
* [Mcrypt](http://at2.php.net/manual/en/mcrypt)

Please refer to http://www.php.net/phpinfo or consult your systems administrator in order to find out if your system fulfills the prerequisites.
