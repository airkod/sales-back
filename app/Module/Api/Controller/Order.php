<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use App\Model\Additional;
use App\Service\Purchase;
use App\Service\WayForPay;
use Light\Exception;
use App\Model;
use Light\Map;

class Order extends Base
{
    /**
     * @return array
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function charge()
    {
        $order = new Model\Order();

        $order->populate([
            'user'   => $this->user,
            'dateTime' => time(),
            'bonus'  => (int)$this->getParam('sum'),
            'status' => Model\Order::STATUS_WAITING,
            'productType' => $this->getParam('productType'),
            'product' => $this->getParam('product')
        ]);

        $order->save();

        return WayForPay::settings($order);
    }

    /**
     * @return array|Map
     */
    public function payments()
    {
        return Map::execute(
            Model\Order::fetchAll([
                'status' => Model\Order::STATUS_PAYED,
                'user' => $this->user
            ]),
            [
                'id' => 'id',
                'bonus' => 'bonus',
                'dateTime' => 'dateTime'
            ]
        );
    }

    /**
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function buyTariff()
    {
        Purchase::buyTariff($this->user, Model\Tariff::fetchOne([
            'type' => $this->getParam('tariff')
        ]));
    }

    /**
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function reBuyTariff()
    {
        Purchase::reBuyTariff($this->user, Model\Tariff::fetchOne([
            'type' => $this->getParam('tariff')
        ]));
    }

    /**
     * @return array|Map
     *
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function buyAdditional()
    {
        $additional = Additional::fetchOne([
            'id' => $this->getParam('additional')
        ]);

        Purchase::buyAdditional($this->user, $additional);

        return Map::execute($additional, [
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'advertisingDescription' => 'advertisingDescription',
            'price' => 'price',
            'file' => function (Additional $additional) {
                return $additional->file;
            },
            'webinar' => 'webinar',
            'active' => function () {
                return true;
            }
        ]);
    }

    /**
     * @throws Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function buyProduct()
    {
        Purchase::buyProduct($this->user, Model\Product::fetchOne([
            'id' => $this->getParam('product')
        ]));
    }

    /**
     * @return array|Map
     */
    public function history()
    {
        return Map::execute(

            Purchase::get($this->user),

            [
                'bonus' => 'bonus',
                'dateTime' => 'dateTime',
                'product' => 'product',
                'data' => function (Model\Purchases $purchase) {

                    if ($purchase->product == Model\Purchases::PRODUCT_PRODUCT) {
                        return Model\Product::fetchOne(['id' => $purchase->data])->title;
                    }

                    if ($purchase->product == Model\Purchases::PRODUCT_ADDITIONAL) {
                        return Model\Additional::fetchOne(['id' => $purchase->data])->title;
                    }

                    else if ($purchase->product == Model\Purchases::PRODUCT_TARIFF) {
                        return Model\Tariff::fetchOne()->title;
                    }
                }
            ]
        );
    }
}
