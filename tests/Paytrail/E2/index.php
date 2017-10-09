<?php
/**
 * User: Yura Zagoruyko
 * Date: 09.10.2017
 * Time: 12:26
 * @url http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/index.php
 */

require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\Object\UrlSet;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Payment;
use Paytrail\Object\Product;
use Paytrail\E2\Http\Client;

function prd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die;
}

$urlSet = new UrlSet;
$urlSet->configure(array(
    'successUrl'      => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/success.php',
    'failureUrl'      => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/canceled.php',
    'notificationUrl' => 'http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/notify.php',
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

$orderNumber = "ORDER-123456";
$payment = new Payment;
$payment->configure(array(
    'orderNumber'     => $orderNumber,
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
    'vat'             => 24.00,
    'discount'        => 0.00,
    'type'            => Product::TYPE_NORMAL,
));

$payment->addProduct($product);
$payment->addPaymentMethods([Payment::PM_NORDEA, Payment::PM_MASTERCARD_NET]);
$payment->setAmount(19.90)
    ->setDescription("Order No.$orderNumber");

$client = new Client('13466', '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ');
$form = $client->buildPaymentForm($payment, false);
echo $form; die;
