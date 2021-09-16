<?php

namespace App\Service;

use App\Model\Additional;
use App\Model\Order;
use App\Model\Product;
use App\Model\Tariff;
use Light\Front;

class WayForPay
{
    /**
     * @param Order $order
     * @return array
     */
    public static function settings(Order $order)
    {
        $config = Front::getInstance()->getConfig()['wayforpay'];

        $productName = self::productName($order);
        $currency = 'UAH';

        return [
            'merchantAccount' => $config['merchantAccount'],
            'merchantDomainName' => $config['merchantDomainName'],
            'authorizationType' => 'SimpleSignature',
            'merchantSignature' => self::signature($order),
            'orderReference' => $order->id,
            'orderDate' => $order->dateTime ?? time(),
            'amount' => $order->bonus,
            'currency' => $currency,
            'productName' => $productName,
            'productPrice' => $order->bonus,
            'productCount' => '1',
            'clientFirstName' => $order->user->name,
            'clientLastName' => $order->user->name,
            'clientEmail' => $order->user->email,
            'clientPhone' => $order->user->phone,
            'serviceUrl' => $config['serviceUrl'],
            'language' => 'RU',
        ];
    }

    /**
     * @param Order $order
     * @param array $data
     *
     * @throws \Light\Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function process(Order $order, array $data)
    {
        $order->paymentData = $data;
        $order->status = Order::STATUS_PAYED;
        $order->dateTime = time();
        $order->save();

        $order->user->bonus = $order->user->bonus + $order->bonus;
        $order->user->save();

        Notification::paymentSuccessful($order);

        switch ($order->productType) {

            case Order::PRODUCT_TYPE_TARIFF:

                Purchase::buyTariff($order->user, Tariff::fetchOne([
                    'type' => $order->product
                ]));
                break;

            case Order::PRODUCT_TYPE_RE_TARIFF:

                Purchase::reBuyTariff($order->user, Tariff::fetchOne([
                    'type' => $order->product
                ]));
                break;

            case Order::PRODUCT_TYPE_ADDITIONAL:

                Purchase::buyAdditional($order->user, Additional::fetchOne([
                    'id' => $order->product
                ]));
                break;

            case Order::PRODUCT_TYPE_PRODUCT:

                Purchase::buyProduct($order->user, Product::fetchOne([
                    'id' => $order->product
                ]));
                break;
        }
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function signature(Order $order)
    {
        $config = Front::getInstance()->getConfig()['wayforpay'];

        $hash = [
            $config['merchantAccount'],
            $config['merchantDomainName'],
            $order->id,
            $order->dateTime,
            $order->bonus,
            'UAH',
            self::productName($order),
            1,
            $order->bonus,
        ];

        return hash_hmac("md5", implode(';', $hash), $config['merchantSecret']);
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function productName(Order $order)
    {
        return 'Пополнение бонусного счета на ' . $order->bonus . ' бон.';
    }
}