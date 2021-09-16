<?php

declare(strict_types=1);

namespace App\Module\Api\Controller;

use App\Service\WayForPay;
use Light\Controller;
use Light\Front;

class Validate extends Controller
{
    /**
     * @return array
     *
     * @throws \Light\Exception
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function index()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $order = \App\Model\Order::fetchOne([
            'id' => $data['orderReference']
        ]);

        if ($data['transactionStatus'] == 'Approved' && $order->status != \App\Model\Order::STATUS_PAYED) {

            WayForPay::process($order, $data);

            $config = Front::getInstance()->getConfig()['wayforpay'];

            $time = time();
            $status = 'accept';
            $signature = hash_hmac("md5", implode(';', [$order->id, $status, $time]), $config['merchantSecret']);

            return [
                'orderReference' => $order->id,
                'status' => $status,
                'time' => $time,
                'signature' => $signature
            ];
        }

        return [];
    }
}
