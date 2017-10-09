<?php
/**
 * Confirming a payment
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:31
 * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/success.php?ORDER_NUMBER=1&TIMESTAMP=1507582948&PAID=9868ff4b06&METHOD=1&RETURN_AUTHCODE=AF812F304CAF534BB1C498D49B5E4B35
 */
require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\REST\Http\Client;

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
$client->connect();
if ($client->validateChecksum($_GET["RETURN_AUTHCODE"], $_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"], $_GET["PAID"], $_GET["METHOD"])) {
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