# paytrail_e2
Paytrail HTTP FORM API E2 client for PHP.

http://docs.paytrail.com/en/index-all.html

### Install using composer

```json
{
  "require": {
    "yurik3zzz/paytrail_e2": "^1.0"
  }
}
```

# Usage E2 API

```php
<?php
require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\Object\UrlSet;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Product;
use Paytrail\E2\Object\Payment;
use Paytrail\E2\Http\Client;

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

```

# Confirming or Canceled a payment E2

```php

<?php
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
```

# Usage REST API

```php
<?php
require(__DIR__ . '/../../../vendor/autoload.php');

use Paytrail\Object\UrlSet;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Product;
use Paytrail\REST\Object\Payment;
use Paytrail\REST\Http\Client;

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

try {
    $result = $client->processPayment($payment);
    header('Location: ' . $result->getUrl());
} catch (Exception $e) {
    //  Paytrail payment failed: Client error: `POST https://payment.paytrail.com/api-payment/create` resulted in a `400 Bad Request` response: {"errorCode":"invalid-order-number","errorMessage":"Missing or invalid order number (ORDER_NUMBER)."}
    die('Paytrail payment failed: ' . $e->getMessage());
}

```

# Confirming a payment REST

```php

<?php
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
```

# Canceled a payment REST

```php
<?php
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
```


# License
MIT. See [LICENSE](LICENSE).
