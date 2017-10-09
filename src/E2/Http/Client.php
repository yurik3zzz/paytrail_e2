<?php
/**
 * This file is part of Paytrail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Paytrail\E2\Http;

use Paytrail\Common\Object;
use Paytrail\Object\Payment;

/**
 * Class Client.
 *
 * @package Paytrail\E2\Http
 */
class Client extends Object
{

    /**
     * Payment gateway address
     *
     * @var string
     */
    const API_ENDPOINT = 'https://payment.paytrail.com/e2';

    /**
     * The Paytrail API key.
     *
     * @var string
     */
    private $_apiKey = '13466';

    /**
     * The Paytrail API secret.
     *
     * @var string
     */
    private $_apiSecret = '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ';

    /**
     * Client constructor.
     *
     * @param string|null $apiKey
     * @param string|null $apiSecret
     */
    public function __construct($apiKey = null, $apiSecret = null)
    {
        if ($apiKey !== null) {
            $this->_apiKey = $apiKey;
        }
        if ($apiSecret !== null) {
            $this->_apiSecret = $apiSecret;
        }
    }

    /**
     * Build HTML Payment form to submit
     * <form action="https://payment.paytrail.com/e2" method="post">
    <input name="MERCHANT_ID" type="hidden" value="13466">
    <input name="CURRENCY" type="hidden" value="EUR">
    <input name="URL_SUCCESS" type="hidden" value="http://www.example.com/success">
    <input name="URL_CANCEL" type="hidden" value="http://www.example.com/cancel">
    <input name="ORDER_NUMBER" type="hidden" value="123456">
    <input name="AMOUNT" type="hidden" value="350.00">
    <input name="PARAMS_IN" type="hidden" value="MERCHANT_ID,CURRENCY,URL_SUCCESS,URL_CANCEL,ORDER_NUMBER,AMOUNT,PARAMS_IN,PARAMS_OUT">
    <input name="PARAMS_OUT" type="hidden" value="PAYMENT_ID,TIMESTAMP,STATUS">
    <input name="AUTHCODE" type="hidden" value="DAA49553843682987B8A03AE1D616DA34A7F596C2B333C4713ECE2745B663896">
    <input type="submit" value="Pay here">
    </form>

     * @param Payment $payment
     * @param bool|true $submit
     * @return string
     */
    public function buildPaymentForm(Payment $payment, $autoSubmit = true)
    {
        $fields = array();
        $fields['MERCHANT_ID'] = $this->_apiKey;
        $fields += $payment->toArray();
        $fields['PARAMS_IN'] = $this->buildParamsIn($fields);
        $fields['AUTHCODE'] = $this->calculateAuthCode($fields);

        $form = '<form id="paytrail-payment-form" action="https://payment.paytrail.com/e2" method="post">';

        foreach ($fields as $key => $value) {
            $form .= '<input name="'.$key.'" type="hidden" value="'.$value.'">';
        }

        if (!$autoSubmit) {
            $form .= '<input type="submit" value="Pay here">';
        }
        $form .= '</form>';

        if ($autoSubmit) {
            $js = '<script>';
            $js .= <<<JS
document.getElementById('paytrail-payment-form').submit();
JS;
            $js .= '</script>';
            $form .= $js;
        }

        return $form;

    }

    /**
     * Validates the given checksum against the order.
     *
     * @param string $checksum Checksum to validate. $_GET["RETURN_AUTHCODE"]
     * @param string $orderNumber The order number. $_GET["ORDER_NUMBER"],
     * @param $paymentId $_GET["PAYMENT_ID"]
     * @param string $amount $_GET["AMOUNT"],
     * @param string $currency
     * @param string $paymentMethod
     * @param int $timestamp The timestamp of the order. $_GET["TIMESTAMP"],
     * @param string $status Payment status, $_GET["STATUS"]
     * @return bool
     */
    public function validateChecksum($checksum, $orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status)
    {
        return $checksum === $this->calculateChecksum($orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status);
    }

    /**
     * Calculates the checksum after response.
     *
     * @param string $orderNumber The order number. $_GET["ORDER_NUMBER"],
     * @param string $paymentId $_GET["PAYMENT_ID"]
     * @param string $amount $_GET["AMOUNT"],
     * @param string $currency
     * @param string $paymentMethod
     * @param int $timestamp The timestamp of the order. $_GET["TIMESTAMP"],
     * @param string $status Payment status, $_GET["STATUS"]
     * @return string
     */
    public function calculateChecksum($orderNumber, $paymentId, $amount, $currency, $paymentMethod, $timestamp, $status)
    {
        $data = array(
            $orderNumber,
            $paymentId,
            $amount,
            $currency,
            $paymentMethod,
            $timestamp,
            $status,
            $this->_apiSecret
        );
        $str = implode('|', $data);
        $hash = hash('sha256', $str);

        return strtoupper($hash);
    }

    /**
     * Set the Paytrail API key.
     *
     * @param string $apiKey The API key.
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
    }

    /**
     * Set the Paytrail API secret.
     *
     * @param string $apiSecret The API secret.
     */
    public function setApiSecret($apiSecret)
    {
        $this->_apiSecret = $apiSecret;
    }

    /**
     * Calculate Hash to form, field "AUTHCODE"
     * @param $array
     * @return string
     */
    private function calculateAuthCode($array)
    {
        $data = [];
        $data[] = $this->_apiSecret;
        foreach ($array as $key => $value) {
            $data[] = $value;
        }

        return strtoupper(hash('sha256', implode('|', $data)));
    }

    /**
     * Build field "PARAMS_IN" to form
     * @param $array
     * @return string
     */
    private function buildParamsIn($array)
    {
        $data = [];
        foreach ($array as $key => $value) {
            $data[] = $key;
        }

        $data[] = 'PARAMS_IN';
        return implode(',', $data);
    }

}
