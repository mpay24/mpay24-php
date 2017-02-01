<?php
require("../bootstrap.php");
use mPay24\Mpay24Soap;

$mpay24 = new Mpay24Soap();

// Each Line is Optional so only add the lines that you need
$tokenizerConfig = array(
	"templateSet" => "DEFAULT",
	"style"       => "DEFAULT",
	"customerID"  => "customer123",
	"profileID"   => "profile123",
	"domain"      => "http://yourdomain.com",
	"language"    => "EN",
);

$tokenizer = $mpay24->createPaymentToken("CC", $tokenizerConfig)->getPaymentResponse();
?>

<iframe src="<?php echo $tokenizer->location; ?>" frameBorder="0"></iframe>

<form action="pay.php" method="POST">
    <input name="token" type="hidden" value="<?php echo $tokenizer->token; ?>"/>
    <button id="paybutton" name="type" value="TOKEN" type="submit" disabled="true">Pay with creditcard</button>
    <button name="type" value="PAYPAL" type="submit">Pay with paypal</button>
</form>

<script>
    window.addEventListener("message", checkValid, false);
    function checkValid(form) {
        var data = JSON.parse(form.data);
        if (data.valid === "true") {
            document.getElementById("paybutton").disabled = false;
        }
    }
</script>
