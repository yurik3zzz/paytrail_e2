<?php
/**
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:32
 * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/canceled.php?ORDER_NUMBER=123456&PAYMENT_ID=101547573685&AMOUNT=19.90&CURRENCY=EUR&PAYMENT_METHOD=&TIMESTAMP=1507565274&STATUS=CANCELLED&RETURN_AUTHCODE=302396B94BA1001F4598F5D756806B301159EAA8B028503EE79B53B59F49E426
 */

require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\E2\Http\Client;

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
if ($client->validateChecksum(
    $_GET["RETURN_AUTHCODE"],
    $_GET["ORDER_NUMBER"],
    $_GET["PAYMENT_ID"],
    $_GET["AMOUNT"],
    $_GET["CURRENCY"],
    $_GET["PAYMENT_METHOD"],
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