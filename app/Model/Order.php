<?php

declare(strict_types = 1);

namespace App\Model;

use Light\Model;

/**
 * @collection Order
 *
 * @property string $id
 *
 * @property integer $bonus
 * @property array   $paymentData
 * @property integer $dateTime
 * @property string  $status
 *
 * @property string $productType
 * @property string $product
 *
 * @property \App\Model\User $user
 *
 * @method static Order[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Order|null fetchOne($cond = null, $sort = null)
 * @method static Order      fetchObject($cond = null, $sort = null)
 */
class Order extends Model
{
    const STATUS_PAYED = 'payed';
    const STATUS_WAITING = 'waiting';

    const PRODUCT_TYPE_TARIFF = 'tariff';
    const PRODUCT_TYPE_RE_TARIFF = 're-tariff';
    const PRODUCT_TYPE_PRODUCT = 'product';
    const PRODUCT_TYPE_ADDITIONAL = 'additional';
    const PRODUCT_TYPE_CHARGE = 'charge';
}
