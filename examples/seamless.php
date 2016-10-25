<?php
include_once ("../lib/MPAY24.php");
$shop = new MPAY24();
$tokenizer = $shop->createPaymentToken("CC")->getPaymentResponse();
?>
<iframe src="<?php echo $tokenizer->location; ?>" frameBorder="0"></iframe>
<form action="pay.php" method="POST">
  <input name="token" type="hidden" value="<? echo $tokenizer->token; ?>" />
  <button id="paybutton" name="type" value="TOKEN" type="submit" disabled="true">Pay with creditcard</button>
  <button name="type" value="PAYPAL" type="submit">Pay with paypal</button>
</form>
<script>
window.addEventListener("message", checkValid, false);
function checkValid(form) {
  var data = JSON.parse(form.data);
  if (data.valid === "true") {
    document.getElementById("paybutton").disabled=false;
  }
}
</script>
