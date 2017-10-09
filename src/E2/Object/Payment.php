<?php
/**
 * This file is part of Paytrail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Paytrail\E2\Object;

use Paytrail\Common\DataObject;
use Paytrail\Exception\PaymentMethodNotSupported;
use Paytrail\Exception\TooManyProducts;
use Paytrail\Exception\CurrencyNotSupported;
use Paytrail\Exception\LocaleNotSupported;
use Paytrail\Object\Product;

/**
 * Class Payment.
 *
 * @package Paytrail\Object
 */
class Payment extends DataObject
{

    /**
     * Currency Euro.
     *
     * @var string CURRENCY_EUR
     */
    const CURRENCY_EUR = 'EUR';

    /**
     * Finnish locale.
     *
     * @var string LOCALE_FIFI
     */
    const LOCALE_FIFI = 'fi_FI';

    /**
     * Swedish locale.
     *
     * @var string LOCALE_SVSE
     */
    const LOCALE_SVSE = 'sv_SE';

    /**
     * English US locale.
     *
     * @var string LOCALE_ENUS
     */
    const LOCALE_ENUS = 'en_US';

    /**
     * VAT included.
     *
     * @var int VAT_MODE_INCLUDED
     */
    const VAT_MODE_INCLUDED = 1;

    /**
     * VAT excluded.
     *
     * @var int VAT_MODE_EXCLUDED
     */
    const VAT_MODE_EXCLUDED = 0;

    /**
     * Max products.
     *
     * @var int MAX_PRODUCT_COUNT
     */
    const MAX_PRODUCT_COUNT = 500;

    /**
     * Nordea, Finnish bank
     * @var int
     */
    const PM_NORDEA = 1;

    /**
     * Osuuspankki	2	Finnish bank
     * @var int
     */
    const PM_COOPERATIVEBANK = 2;

    /**
     * Danske Bank	3	Finnish bank
     * @var int
     */
    const PM_DANSKEBANK = 3;

    /**
     * Ålandsbanken	5	Finnish bank
     * @var int
     */
    const PM_ALANDSBANK = 5;

    /**
     * Handelsbanken	6	Finnish bank
     * @var int
     */
    const PM_HANDELSBANKEN = 6;

    /**
     * PayPal, debit/credit card
     * Only available as interface; requires own agreement with PayPal and is hidden until credentials are saved in Merchant's Panel
     */
    const PM_PAYPAL = 9;

    /**
     * S-Pankki	10	Finnish bank
     */
    const PM_SPANKKI = 10;

    /**
     * Klarna Invoice	11	Invoice	Hidden
     * if PAYER_PERSON_PHONE is not defined. Only available as interface;
     * requires own agreement with Klarna and is hidden until credentials are saved in Merchant's Panel.
     * Payment fails if product details or payer details (name, address, phone number) are missing
     */
    const PM_KLARNA_INVOICE = 11;

    /**
     * Klarna Installment	12	Invoice	Hidden
     * if PAYER_PERSON_PHONE is not defined. Only available as interface;
     * requires own agreement with Klarna and is hidden until credentials are saved in Merchant's Panel.
     * Payment fails if product details or payer details (name, address, phone number) are missing
     */
    const PM_KLARNA_INSTALLMENT = 12;

    /**
     * Jousto	18	Invoice	Hidden
     * if amount is less than 20 € or greater than 5000 €
     */
    const PM_JOUSTO = 18;

    /**
     * Visa	30	debit/credit card
     */
    const PM_VISA = 30;

    /**
     * MasterCard	31	debit/credit card
     */
    const PM_MASTERCARD = 31;

    /**
     * Diners Club	34	credit card
     */
    const PM_DINERS_CLUB = 34;

    /**
     * JCB	35	debit/credit card
     */
    const PM_JCB = 35;

    /**
     * Paytrail account	36	debit/credit card
     */
    const PM_PAYTRAIL = 36;

    /**
     * Aktia	50	Finnish bank
     */
    const PM_AKTIA = 50;

    /**
     * POP Pankki	51	Finnish bank
     */
    const PM_POP_PANKKI = 51;

    /**
     * Säästöpankki	52	Finnish bank
     */
    const PM_SAVINGSBANK = 52;

    /**
     * Visa, debit/credit card
     */
    const PM_VISA_NET = 53;

    /**
     * MasterCard, debit/credit card
     */
    const PM_MASTERCARD_NET = 54;

    /**
     * Diners Club (Nets)	55	credit card
     */
    const PM_DINERSCLUB_NET = 55;

    /**
     * American Express (Nets)	56	credit card
     */
    const PM_AMERICANEXPRESS_NET = 56;

    /**
     * MobilePay	58	debit/credit card
     */
    const PM_MOBILEPAY = 58;

    /**
     * Collector Bank	60	invoice	Hidden
     * if VAT_IS_INCLUDED value is not 1, ITEM_AMOUNT[n] is not whole number, amount is greater than 5000 €,
     * product details are missing. Payment fails if payer details (name, address) are missing
     */
    const PM_COLLECTORBANK = 60;

    /**
     * Oma Säästöpankki	61	Finnish bank
     */
    const PM_MYSAVINGSBANK = 61;

    /**
     * The order number.
     *
     * @var string $orderNumber
     */
    protected $orderNumber;

    /**
     * @var string
     */
    protected $amount;

    /**
     * The reference number.
     *
     * @var string $referenceNumber
     */
    protected $referenceNumber = '';

    /**
     * Description.
     *
     * @var string $description
     */
    protected $description = '';

    /**
     * Currency, defaults to Euro.
     *
     * @var string $currency
     */
    protected $currency = self::CURRENCY_EUR;

    /**
     * The locale, defaults to Finnish.
     *
     * @var string $locale
     */
    protected $locale = self::LOCALE_ENUS;

    /**
     * VAT mode, defaults to VAT included.
     *
     * @var int $vatMode
     */
    protected $vatMode = self::VAT_MODE_INCLUDED;

    /**
     * The contact object.
     *
     * @var \Paytrail\Object\Contact $contact
     */
    protected $contact;

    /**
     * The URL set object.
     *
     * @var \Paytrail\Object\UrlSet $urlSet
     */
    protected $urlSet;

    /**
     * List of product objects.
     *
     * @var \Paytrail\Object\Product[] $products
     */
    protected $products = array();

    /**
     * List of supported currencies.
     *
     * @var array $supportedCurrencies
     */
    static $supportedCurrencies = array(
        self::CURRENCY_EUR,
    );

    /**
     * List of supported locales.
     *
     * @var array $supportedLocales
     */
    static $supportedLocales = array(
        self::LOCALE_FIFI,
        self::LOCALE_SVSE,
        self::LOCALE_ENUS,
    );

    protected $paymentMethods = array();

    /**
     * List of supported payment methods.
     * @url http://docs.paytrail.com/en/ch05s04.html
     *
     * @var array $supportedLocales
     */
    static $supportedPaymentMethods = array(
        self::PM_NORDEA,
        self::PM_COOPERATIVEBANK,
        self::PM_DANSKEBANK,
        self::PM_ALANDSBANK,
        self::PM_HANDELSBANKEN,
        self::PM_PAYPAL,
        self::PM_SPANKKI,
        self::PM_KLARNA_INVOICE,
        self::PM_KLARNA_INSTALLMENT,
        self::PM_JOUSTO,
        self::PM_VISA,
        self::PM_MASTERCARD,
        self::PM_DINERS_CLUB,
        self::PM_JCB,
        self::PM_PAYTRAIL,
        self::PM_AKTIA,
        self::PM_POP_PANKKI,
        self::PM_SAVINGSBANK,
        self::PM_VISA_NET,
        self::PM_MASTERCARD_NET,
        self::PM_DINERSCLUB_NET,
        self::PM_AMERICANEXPRESS_NET,
        self::PM_MOBILEPAY,
        self::PM_COLLECTORBANK,
        self::PM_MYSAVINGSBANK,
    );

    /**
     * Add a product.
     *
     * @param \Paytrail\Object\Product $product The product to add.
     *
     * @throws \Paytrail\Exception\TooManyProducts
     */
    public function addProduct(Product $product)
    {
        if (count($this->products) > self::MAX_PRODUCT_COUNT) {
            throw new TooManyProducts(
                sprintf(
                    'Paytrail can only handle up to %d different products. Please group products using "amount".',
                    self::MAX_PRODUCT_COUNT
                )
            );
        }
        $this->products[] = $product;
    }

    /**
     * Convert the payment object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $array['LOCALE'] = $this->locale;
        $array['URL_SUCCESS'] = $this->urlSet->getSuccessUrl();
        $array['URL_CANCEL'] = $this->urlSet->getFailureUrl();
        $array['ORDER_NUMBER'] = $this->orderNumber;
        $array['AMOUNT'] = $this->amount;
        $array['CURRENCY'] = $this->currency;
        $array['PARAMS_OUT'] = 'ORDER_NUMBER,PAYMENT_ID,AMOUNT,CURRENCY,PAYMENT_METHOD,TIMESTAMP,STATUS';

        if (!empty($this->referenceNumber)) {
            $array['REFERENCE_NUMBER'] = $this->referenceNumber;
        }

        // Field PAYMENT_METHODS is a comma separated list of payment methods visible at payment method selection page.
        // If only one is set the payment method page is bypassed and payer is directed to payment method provider page.
        if (!empty($this->paymentMethods)) {
            $array['PAYMENT_METHODS'] = implode(',', $this->paymentMethods);
        }

        if (!empty($this->description)) {
            $array['MSG_UI_MERCHANT_PANEL'] = $this->description;
        }

        if ($this->contact !== null) {
            $array['PAYER_PERSON_FIRSTNAME'] = $this->contact->getFirstName();
            $array['PAYER_PERSON_LASTNAME'] = $this->contact->getLastName();
            $array['PAYER_PERSON_EMAIL'] = $this->contact->getEmail();
            $array['PAYER_PERSON_PHONE'] = $this->contact->getPhoneNumber();
            $array['PAYER_COMPANY_NAME'] = $this->contact->getCompanyName();
            $array['PAYER_PERSON_ADDR_STREET'] = $this->contact->getAddress()->getStreetAddress();
            $array['PAYER_PERSON_ADDR_POSTAL_CODE'] = $this->contact->getAddress()->getPostalCode();
            $array['PAYER_PERSON_ADDR_TOWN'] = $this->contact->getAddress()->getPostalOffice();
            $array['PAYER_PERSON_ADDR_COUNTRY'] = $this->contact->getAddress()->getCountryCode();
        }

        if (!empty($this->products)) {
            $array['VAT_IS_INCLUDED'] = $this->vatMode;
            foreach ($this->products as $key => $product) {
                $array["ITEM_TITLE[$key]"] = $product->getTitle();
                $array["ITEM_ID[$key]"] = $product->getCode();
                $array["ITEM_QUANTITY[$key]"] = $product->getAmount();
                $array["ITEM_UNIT_PRICE[$key]"] = $product->getPrice();
                $array["ITEM_VAT_PERCENT[$key]"] = $product->getVat();
                $array["ITEM_DISCOUNT_PERCENT[$key]"] = $product->getDiscount();
                $array["ITEM_TYPE[$key]"] = $product->getType();
            }
        }

        return $array;

    }

    /**
     * Get order number.
     *
     * @return string The order number.
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Get reference number.
     *
     * @return string The reference number.
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Get description.
     *
     * @return string The description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the currency.
     *
     * @param string $currency Currency to set.
     * @return $this
     * @throws \Paytrail\Exception\CurrencyNotSupported
     */
    public function setCurrency($currency)
    {
        if ( ! in_array($currency, self::$supportedCurrencies)) {
            throw new CurrencyNotSupported(sprintf('Currency "%s" is not supported.', $currency));
        }
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency.
     *
     * @return string The currency.
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the locale.
     *
     * @param string $locale The locale to set.
     *
     * @return $this
     * @throws @throws \Paytrail\Exception\LocaleNotSupported
     */
    public function setLocale($locale)
    {
        if ( ! in_array($locale, self::$supportedLocales)) {
            throw new LocaleNotSupported(sprintf('Locale "%s" is not supported.', $locale));
        }
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get locale.
     *
     * @return string The locale.
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get VAT mode.
     *
     * @return int The VAT mode.
     */
    public function getVatMode()
    {
        return $this->vatMode;
    }

    /**
     * Get contact.
     *
     * @return \Paytrail\Object\Contact The contact object.
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Get URL set.
     *
     * @return \Paytrail\Object\UrlSet The URLSet object.
     */
    public function getUrlSet()
    {
        return $this->urlSet;
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * Add payments method
     * @param $method
     * @return $this
     * @throws PaymentMethodNotSupported
     */
    public function addPaymentMethod($method)
    {
        if (!in_array($method, self::$supportedPaymentMethods)) {
            throw new PaymentMethodNotSupported(sprintf('Payment method "%s" is not supported.', $method));
        }
        $this->paymentMethods[] = $method;
        return $this;
    }

    /**
     * Add payments methods
     * @param array $methods
     * @return $this
     * @throws PaymentMethodNotSupported
     */
    public function addPaymentMethods(array $methods = [])
    {
        foreach ($methods as $method) {
            $this->addPaymentMethod($method);
        }

        return $this;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $amount = number_format($amount, 2, '.', '');
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

}
