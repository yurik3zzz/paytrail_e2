<?php
/**
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:26
 * @url http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/index.php
 */

require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\Object\UrlSet;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Product;
use Paytrail\REST\Object\Payment;
use Paytrail\REST\Http\Client;

function prd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die;
}

$urlSet = new UrlSet;
$urlSet->configure(array(
    'successUrl'      => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/success.php',
    'failureUrl'      => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/canceled.php',
    'notificationUrl' => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/REST/notify.php',
));

$address = new Address;
$address->configure(array(
    'streetAddress'   => 'Test street 1',
    'postalCode'      => '12345',
    'postalOffice'    => 'Helsinki',
    'countryCode'     => 'FI',
));

$contact = new Contact;
$contact->configure(array(
    'firstName'       => 'Test',
    'lastName'        => 'Person',
    'email'           => 'test.person@demoshop.com',
    'phoneNumber'     => '040123456',
    'companyName'     => 'Demo Company Ltd',
    'address'         => $address,
));

$payment = new Payment;
$payment->configure(array(
    'orderNumber'     => 1,
    'urlSet'          => $urlSet,
    'contact'         => $contact,
    'locale'          => Payment::LOCALE_ENUS,
));

$product = new Product;
$product->configure(array(
    'title'           => 'Test product',
    'code'            => '01234',
    'amount'          => 1.00,
    'price'           => 19.90,
    'vat'             => 23.00,
    'discount'        => 0.00,
    'type'            => Product::TYPE_NORMAL,
));

$payment->addProduct($product);

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
$client->connect();
//prd($payment->toArray());

try {
    $result = $client->processPayment($payment);
    header('Location: ' . $result->getUrl());
} catch (Exception $e) {
    //  Paytrail payment failed: Client error: `POST https://payment.paytrail.com/api-payment/create` resulted in a `400 Bad Request` response: {"errorCode":"invalid-order-number","errorMessage":"Missing or invalid order number (ORDER_NUMBER)."}
    die('Paytrail payment failed: ' . $e->getMessage());
}
