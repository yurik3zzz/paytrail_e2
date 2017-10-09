<?php
/**
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:32
 * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/canceled.php?ORDER_NUMBER=1&TIMESTAMP=1507583075&RETURN_AUTHCODE=A8C597A46E0FA1631661D6C16274987F
 */

require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\REST\Http\Client;

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
$client->connect();
if ($client->validateChecksum($_GET["RETURN_AUTHCODE"], $_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"])) {
    // Payment receipt is valid
    // If needed, the used payment method can be found from the variable $_GET["METHOD"]
    // and order number for the payment from the variable $_GET["ORDER_NUMBER"]
    echo "Valid ";
    print_r($_GET);
}
else {
    // Payment receipt was not valid, possible payment fraud attempt
    echo "Invalid ";
    print_r($_GET);
}