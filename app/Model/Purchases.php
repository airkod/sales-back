<?php

declare(strict_types = 1);

namespace App\Model;

use Light\Model;

/**
 * @collection Purchases
 *
 * @property string $id
 *
 * @property integer $bonus
 * @property string  $product
 * @property string  $data
 * @property integer $dateTime
 *
 * @property \App\Model\User $user
 *
 * @method static Purchases[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Purchases|null fetchOne($cond = null, $sort = null)
 * @method static Purchases      fetchObject($cond = null, $sort = null)
 */
class Purchases extends Model
{
    const PRODUCT_TARIFF = 'tariff';
    const PRODUCT_PRODUCT = 'product';
    const PRODUCT_ADDITIONAL = 'additional';
}
