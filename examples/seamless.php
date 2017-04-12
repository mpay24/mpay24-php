<?php
require("../bootstrap.php");

use Mpay24\Mpay24;

$mpay24 = new Mpay24();

// Each Line is Optional so only add the lines that you need
$tokenizerConfig = array(
    "language"    => "EN",
);

$tokenizer = $mpay24->token("CC", $tokenizerConfig);
?>

<iframe src="<?php echo $tokenizer->getLocation(); ?>" frameBorder="0"></iframe>

<form action="pay.php" method="POST">
    <input name="token" type="hidden" value="<?php echo $tokenizer->getToken(); ?>"/>
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
