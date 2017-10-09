<?php

namespace Paytrail\E2;

/**
 * This file is part of Paytrail E2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Paytrail\E2\Http\Client;
use Paytrail\E2\Object\Payment;
use Paytrail\Exception\PaymentMethodNotSupported;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Product;
use Paytrail\Object\UrlSet;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test payment checksum validation on successful transaction.
     * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/success.php?ORDER_NUMBER=123456&PAYMENT_ID=107111952351&AMOUNT=19.90&CURRENCY=EUR&PAYMENT_METHOD=1&TIMESTAMP=1507562251&STATUS=PAID&RETURN_AUTHCODE=ED8808A7336319878C0C198F0FEB19BB6AE4DC18B924369B2042E0F7D76A1DBD
     */
    public function testValidateSuccessChecksum()
    {
        $client = $this->makeClient();

        $orderNumber    = "IOT0910178";
        $paymentId      = "107691197872";
        $amount         = "2.49";
        $currency       = "EUR";
        $paymentMethod  = "1";
        $timestamp      = "1507575730";
        $status         = 'PAID';
        $returnAuthCode = 'B98B0FBFA7C07EDBF29734348B6BF00A2FFB3AAA9C93C0809F9AF342ED3E3618';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status));
    }

    /**
     * Test payment checksum validation on successful transaction.
     */
    public function testValidateSuccessChecksum2()
    {
        $client = $this->makeClient();

        $orderNumber    = "IOT0910179";
        $paymentId      = "104230263940";
        $amount         = "9.12";
        $currency       = "EUR";
        $paymentMethod  = "1";
        $timestamp      = "1507576148";
        $status         = 'PAID';
        $returnAuthCode = 'D2EDDE6E87B23079D98310645C8EBE475EB6DE08277B219C0B21ACB29C4C44EA';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status));
    }

    /**
     * Test payment checksum validation on canceled transaction.
     */
    public function testValidateCanceledChecksum()
    {
        $client = $this->makeClient();

        $orderNumber    = "IOT09101710";
        $paymentId      = "109928646780";
        $amount         = "10.77";
        $currency       = "EUR";
        $paymentMethod  = "";
        $timestamp      = "1507576505";
        $status         = 'CANCELLED';
        $returnAuthCode = '36E57FD43E3985FB9750598BF551C4A3BFB751CA4CDD63CD429D2B7B08DEDFFA';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status));
    }

    /**
     * Validate checksum validation on failed trasaction.
     * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/success.php?ORDER_NUMBER=123456&PAYMENT_ID=108654537643&AMOUNT=19.90&CURRENCY=EUR&PAYMENT_METHOD=1&TIMESTAMP=1507564222&STATUS=PAID&RETURN_AUTHCODE=C7450731D359FF15EE65884CD884D27B2528CAB5A58D3DDA51E8C77DCFFCA06E
     */
    public function testValidateFailureChecksum()
    {
        $client = $this->makeClient();

        $orderNumber    = "-=IOT0910179=-";
        $paymentId      = "104230263940";
        $amount         = "9.12";
        $currency       = "EUR";
        $paymentMethod  = "1";
        $timestamp      = "1507576148";
        $status         = 'PAID';
        $returnAuthCode = 'D2EDDE6E87B23079D98310645C8EBE475EB6DE08277B219C0B21ACB29C4C44EA';

        $this->assertFalse($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status));
    }

    /**
     * Test setting of Payment Method
     *
     * @throws PaymentMethodNotSupported
     */
    public function testSetPaymentMethod()
    {
        $payment = $this->makePayment();
        $payment->addPaymentMethod(Payment::PM_NORDEA);
        $this->assertTrue(in_array(1, $payment->getPaymentMethods()));
    }

    /**
     * Test setting of Payment Method
     *
     * @throws PaymentMethodNotSupported
     */
    public function testSetPaymentMethods()
    {
        $payment = $this->makePayment();
        $payment->addPaymentMethods([Payment::PM_NORDEA, Payment::PM_VISA_NET]);
        $this->assertTrue(in_array(1, $payment->getPaymentMethods()));
        $this->assertTrue(in_array(53, $payment->getPaymentMethods()));
    }

    /**
     * Test setting of PaymentMethod Failure
     *
     * @throws PaymentMethodNotSupported
     */
    public function testFailSetPaymentMethod()
    {
        $payment = $this->makePayment();
        try {
            $payment->addPaymentMethod(4);
        } catch (\Exception $e) {
            // this is the expected outcome.
            $this->assertTrue($e instanceof PaymentMethodNotSupported);
        }
    }

    /**
     * Creates the UrlSet.
     *
     * @return UrlSet
     */
    protected function makeUrlSet()
    {
        $urlSet = new UrlSet;
        $urlSet->configure(array(
            'successUrl'      => 'https://www.demoshop.com/sv/success',
            'failureUrl'      => 'https://www.demoshop.com/sv/failure',
            'notificationUrl' => 'https://www.demoshop.com/sv/notify',
        ));

        return $urlSet;
    }

    /**
     * Creates an Address.
     *
     * @return Address
     */
    protected function makeAddress()
    {
        $address = new Address;
        $address->configure(array(
            'streetAddress' => 'Test street 1',
            'postalCode'    => '12345',
            'postalOffice'    => 'Helsinki',
            'countryCode'   => 'FI',
        ));

        return $address;
    }

    /**
     * Creates a Contact.
     *
     * @return Contact
     */
    protected function makeContact()
    {
        $contact = new Contact;
        $contact->configure(array(
            'firstName'   => 'Test',
            'lastName'    => 'Person',
            'email'       => 'test.person@demoshop.com',
            'phoneNumber' => '040123456',
            'companyName' => 'Demo Company Ltd',
            'address'     => $this->makeAddress(),
        ));

        return $contact;
    }

    /**
     * Creates a Payment.
     *
     * @return Payment
     *
     * @throws \Paytrail\Exception\TooManyProducts
     */
    protected function makePayment()
    {
        $payment = new Payment;
        $payment->configure(array(
            'orderNumber' => 1,
            'urlSet'      => $this->makeUrlSet(),
            'contact'     => $this->makeContact(),
            'locale'      => Payment::LOCALE_ENUS,
            'amount'      => 19.90
        ));
        $payment->addProduct($this->makeProduct());

        return $payment;
    }

    /**
     * Creates a Product.
     *
     * @return Product
     */
    protected function makeProduct()
    {
        $product = new Product;
        $product->configure(array(
            'title'    => 'Test product',
            'code'     => '01234',
            'amount'   => 1.00,
            'price'    => 19.90,
            'vat'      => 23.00,
            'discount' => 0.00,
            'type'     => Product::TYPE_NORMAL,
        ));

        return $product;
    }

    /**
     * Creates a Client.
     *
     * @return Client
     */
    protected function makeClient()
    {
        return new Client();
    }
}