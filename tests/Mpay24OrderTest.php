<?php

namespace Mpay24Test;

use Mpay24\Mpay24Order;
use PHPUnit\Framework\TestCase;

/**
 * Class Mpay24OrderTest
 * @package    Mpay24Test
 *
 * @author     Stefan Polzer <develop@ps-webdesign.com>
 * @filesource Mpay24OrderTest.php
 * @license    MIT
 */
class Mpay24OrderTest extends TestCase
{
    public function testMpay24OrderClassBasic()
    {
        $mdxi = new Mpay24Order();

        $mdxi->Order->Tid               = '123 TID';
        $mdxi->Order->Price             = '1.00';
        $mdxi->Order->URL->Success      = 'http://yourdomain.com/success';
        $mdxi->Order->URL->Error        = 'http://yourdomain.com/error';
        $mdxi->Order->URL->Confirmation = 'http://yourdomain.com/confirmation';

        $xml = array_map('trim', explode("\n", $mdxi->toXML()));

        $this->assertGreaterThanOrEqual(10, count($xml));

        $this->assertSame('<?xml version="1.0" encoding="UTF-8"?>', $xml[0]);
        $this->assertSame('<Order>', $xml[1]);
        $this->assertSame('<Tid>123 TID</Tid>', $xml[2]);
        $this->assertSame('<Price>1.00</Price>', $xml[3]);
        $this->assertSame('<URL>', $xml[4]);
        $this->assertSame('<Success>http://yourdomain.com/success</Success>', $xml[5]);
        $this->assertSame('<Error>http://yourdomain.com/error</Error>', $xml[6]);
        $this->assertSame('<Confirmation>http://yourdomain.com/confirmation</Confirmation>', $xml[7]);
        $this->assertSame('</URL>', $xml[8]);
        $this->assertSame('</Order>', $xml[9]);
    }

    public function testMpay24OrderClassComplex()
    {
        $mdxi = new Mpay24Order();
        $mdxi->Order->setLogoStyle("");

        $mdxi->Order->UserField = "My User Field";
        $mdxi->Order->Tid       = "My Transaction ID";

        $mdxi->Order->TemplateSet->setLanguage("DE");
        $mdxi->Order->TemplateSet->setCSSName("MODERN");

        //pre-selection of payment system
        $mdxi->Order->PaymentTypes->setEnable("true");
        $mdxi->Order->PaymentTypes->Payment(1)->setType("CC");
        $mdxi->Order->PaymentTypes->Payment(1)->setBrand("VISA");

        $mdxi->Order->ShoppingCart->Description = "Order Description";

        $mdxi->Order->ShoppingCart->Item(1)->Number      = "Item Number 1";
        $mdxi->Order->ShoppingCart->Item(1)->ProductNr   = "Product Number 1";
        $mdxi->Order->ShoppingCart->Item(1)->Description = "Description 1";
        $mdxi->Order->ShoppingCart->Item(1)->Package     = "Package 1";
        $mdxi->Order->ShoppingCart->Item(1)->Quantity    = 2;
        $mdxi->Order->ShoppingCart->Item(1)->ItemPrice   = 12.34;
        $mdxi->Order->ShoppingCart->Item(1)->ItemPrice->setTax(1.23);
        $mdxi->Order->ShoppingCart->Item(1)->Price = 24.68;

        $mdxi->Order->ShoppingCart->Item(2)->Number      = "Item Number 2";
        $mdxi->Order->ShoppingCart->Item(2)->ProductNr   = "Product Number 2";
        $mdxi->Order->ShoppingCart->Item(2)->Description = "Description 2";
        $mdxi->Order->ShoppingCart->Item(2)->Package     = "Package 2";
        $mdxi->Order->ShoppingCart->Item(2)->Quantity    = 1;
        $mdxi->Order->ShoppingCart->Item(2)->ItemPrice   = 5.67;
        $mdxi->Order->ShoppingCart->Item(2)->Price       = 5.67;

        $mdxi->Order->Price = 30.35;

        $mdxi->Order->Currency = "USD";

        $mdxi->Order->Customer->setUseProfile("true");
        $mdxi->Order->Customer->setId("98765");
        $mdxi->Order->Customer = "Hans Mayer";

        $mdxi->Order->BillingAddr->setMode("ReadOnly");
        $mdxi->Order->BillingAddr->Name    = "Max Musterman";
        $mdxi->Order->BillingAddr->Street  = "Teststreet 1";
        $mdxi->Order->BillingAddr->Street2 = "Teststreet 2";
        $mdxi->Order->BillingAddr->Zip     = "1010";
        $mdxi->Order->BillingAddr->City    = "Wien";
        $mdxi->Order->BillingAddr->Country->setCode("AT");
        $mdxi->Order->BillingAddr->Email = "a.b@c.de";

        $xml = array_map('trim', explode("\n", $mdxi->toXML()));

        $this->assertGreaterThanOrEqual(42, count($xml));

        $this->assertSame('<?xml version="1.0" encoding="UTF-8"?>', $xml[0]);
        $this->assertSame('<Order LogoStyle="">', $xml[1]);
        $this->assertSame('<UserField>My User Field</UserField>', $xml[2]);
        $this->assertSame('<Tid>My Transaction ID</Tid>', $xml[3]);
        $this->assertSame('<TemplateSet Language="DE" CSSName="MODERN"/>', $xml[4]);
        $this->assertSame('<PaymentTypes Enable="true">', $xml[5]);
        $this->assertSame('<Payment Type="CC" Brand="VISA"/>', $xml[6]);
        $this->assertSame('</PaymentTypes>', $xml[7]);
        $this->assertSame('<ShoppingCart>', $xml[8]);
        $this->assertSame('<Description>Order Description</Description>', $xml[9]);
        $this->assertSame('<Item>', $xml[10]);
        $this->assertSame('<Number>Item Number 1</Number>', $xml[11]);
        $this->assertSame('<ProductNr>Product Number 1</ProductNr>', $xml[12]);
        $this->assertSame('<Description>Description 1</Description>', $xml[13]);
        $this->assertSame('<Package>Package 1</Package>', $xml[14]);
        $this->assertSame('<Quantity>2</Quantity>', $xml[15]);
        $this->assertSame('<ItemPrice Tax="1.23">12.34</ItemPrice>', $xml[16]);
        $this->assertSame('<Price>24.68</Price>', $xml[17]);
        $this->assertSame('</Item>', $xml[18]);
        $this->assertSame('<Item>', $xml[19]);
        $this->assertSame('<Number>Item Number 2</Number>', $xml[20]);
        $this->assertSame('<ProductNr>Product Number 2</ProductNr>', $xml[21]);
        $this->assertSame('<Description>Description 2</Description>', $xml[22]);
        $this->assertSame('<Package>Package 2</Package>', $xml[23]);
        $this->assertSame('<Quantity>1</Quantity>', $xml[24]);
        $this->assertSame('<ItemPrice>5.67</ItemPrice>', $xml[25]);
        $this->assertSame('<Price>5.67</Price>', $xml[26]);
        $this->assertSame('</Item>', $xml[27]);
        $this->assertSame('</ShoppingCart>', $xml[28]);
        $this->assertSame('<Price>30.35</Price>', $xml[29]);
        $this->assertSame('<Currency>USD</Currency>', $xml[30]);
        $this->assertSame('<Customer UseProfile="true" Id="98765">Hans Mayer</Customer>', $xml[31]);
        $this->assertSame('<BillingAddr Mode="ReadOnly">', $xml[32]);
        $this->assertSame('<Name>Max Musterman</Name>', $xml[33]);
        $this->assertSame('<Street>Teststreet 1</Street>', $xml[34]);
        $this->assertSame('<Street2>Teststreet 2</Street2>', $xml[35]);
        $this->assertSame('<Zip>1010</Zip>', $xml[36]);
        $this->assertSame('<City>Wien</City>', $xml[37]);
        $this->assertSame('<Country Code="AT"/>', $xml[38]);
        $this->assertSame('<Email>a.b@c.de</Email>', $xml[39]);
        $this->assertSame('</BillingAddr>', $xml[40]);
        $this->assertSame('</Order>', $xml[41]);
    }
}
