<?php
require("../bootstrap.php");

use Mpay24\Mpay24;

$mpay24 = new Mpay24();

$tokenizerConfig = array( "language" => "EN");
$tokenizer = $mpay24->token("CC", $tokenizerConfig);

?>

<iframe src="<?php echo $tokenizer->getLocation(); ?>" frameBorder="0" style="width:500px;"></iframe>

<form method="POST">
    <input name="token" type="hidden" value="<?php echo $tokenizer->getToken(); ?>"/>
    <button name="type" value="TOKEN" type="submit">Create Profile</button>
</form>
</br>
<?php

if (isset($_POST["token"])) {
  $payment = array(
      "token" => $_POST["token"]
  );

  $additional = array(
    "billingAddress" => array(
      "mode" => "READONLY",
      "name" => "John Doe",
      "street" => "Mainstreet 1",
      "zip" => "1050",
      "city" => "Vienna",
      "countryCode" => "AT"
    )
  );
  $result = $mpay24->createCustomer("TOKEN", "123456", $payment, $additional);

  echo $result->getReturnCode();
}

?>
