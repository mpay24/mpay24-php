# mPAY24 PHP API
## Open the mPAY24 pay page in 5 steps!
### [See the steps](https://github.com/mPAY24/mpay24_php_api/wiki/STEP-1)
***
### ABSTRACT

The target of this guide is to help you open the [mPAY24](https://www.mpay24.com) payment page in five simple steps, using the mPAY24 PHP API.

*Please note, that you still need to implement the mPAY24 Confirmation-Interface as explained in the "Specification of the mPAY24 Interfaces" in order to have your system process the result of the payment transaction!* The mPAY24 PHP API will also help you with this, but in order to fully understand how payment transactions work and therefore avoid some common pitfalls with the implementation, you are strongly encouraged to refer to the specification! There is also a complete and ready to test "example shop" available at mPAY24.

### Prerequisites

In order for the mPAY24 PHP API to work, your installation will have to meet the following prerequisites:

* [PHP >= 5](http://www.php.net/)
* [cURL](http://at2.php.net/manual/de/book.curl.php)
* [DOM](http://at2.php.net/manual/de/book.dom.php)
* [Mcrypt](http://at2.php.net/manual/en/mcrypt)

Please refer to http://www.php.net/phpinfo or consult your systems administrator in order to find out if your system fulfills the prerequisites.
