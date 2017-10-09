<?php
/**
 * Confirming a payment
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:31
 * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/success.php?ORDER_NUMBER=123456&PAYMENT_ID=107111952351&AMOUNT=19.90&CURRENCY=EUR&PAYMENT_METHOD=1&TIMESTAMP=1507562251&STATUS=PAID&RETURN_AUTHCODE=ED8808A7336319878C0C198F0FEB19BB6AE4DC18B924369B2042E0F7D76A1DBD
 */
require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\E2\Http\Client;

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
if ($client->validateChecksum(
    $_GET["RETURN_AUTHCODE"],
    $_GET["ORDER_NUMBER"],
    $_GET["PAYMENT_ID"],
    $_GET["AMOUNT"],
    $_GET["TIMESTAMP"],
    $_GET["STATUS"]
)) {
    // Payment receipt is valid
    echo "Valid ";
    print_r($_GET);
}
else {
    // Payment receipt was not valid, possible payment fraud attempt
    echo "Invalid ";
    print_r($_GET);
}