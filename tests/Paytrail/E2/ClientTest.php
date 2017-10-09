<?php

namespace Paytrail;

/**
 * This file is part of Paytrail E2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Paytrail\E2\Http\Client;
use Paytrail\Exception\PaymentMethodNotSupported;
use Paytrail\Object\Address;
use Paytrail\Object\Contact;
use Paytrail\Object\Payment;
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

        $orderNumber    = "ORDER-12345";
        $paymentId      = "123456789012";
        $amount         = "200.00";
        $timestamp      = "1491896573";
        $status         = 'PAID';
        $returnAuthCode = '86CC6A9B9433D3AC1D8D1B8D21ED87DA3ABE2E980D3F826D1901FEF0925F5D03';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $timestamp, $status));
    }

    /**
     * Test payment checksum validation on successful transaction.
     */
    public function testValidateSuccessChecksum2()
    {
        $client = $this->makeClient();

        $orderNumber    = "ORDER-12345";
        $paymentId      = "100800639898";
        $amount         = "19.90";
        $timestamp      = "1507565971";
        $status         = 'PAID';
        $returnAuthCode = '004126104CA70D2373DA6CFFB32991D57571DC870F2C2CE3E42631E6301E909C';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $timestamp, $status));
    }

    /**
     * Test payment checksum validation on canceled transaction.
     */
    public function testValidateCanceledChecksum()
    {
        $client = $this->makeClient();

        $orderNumber    = "123456";
        $paymentId      = "101547573685";
        $amount         = "19.90";
        $timestamp      = "1507565274";
        $status         = 'CANCELLED';
        $returnAuthCode = '302396B94BA1001F4598F5D756806B301159EAA8B028503EE79B53B59F49E426';

        $this->assertTrue($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $timestamp, $status));
    }

    /**
     * Validate checksum validation on failed trasaction.
     * http://yurik3zzz/github/paytrail_e2/tests/Paytrail/E2/success.php?ORDER_NUMBER=123456&PAYMENT_ID=108654537643&AMOUNT=19.90&CURRENCY=EUR&PAYMENT_METHOD=1&TIMESTAMP=1507564222&STATUS=PAID&RETURN_AUTHCODE=C7450731D359FF15EE65884CD884D27B2528CAB5A58D3DDA51E8C77DCFFCA06E
     */
    public function testValidateFailureChecksum()
    {
        $client = $this->makeClient();

        $orderNumber    = "-=123456=-";
        $paymentId      = "108654537643";
        $amount         = 19.90;
        $timestamp      = 1507564222;
        $status         = 'PAID';
        $returnAuthCode = 'C7450731D359FF15EE65884CD884D27B2528CAB5A58D3DDA51E8C77DCFFCA06E';

        $this->assertFalse($client->validateChecksum($returnAuthCode, $orderNumber, $paymentId, $amount, $timestamp, $status));
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