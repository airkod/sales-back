<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Tariff
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property boolean $popular
 * @property string $type
 *
 * @property integer $price
 *
 * @method static Tariff[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Tariff|null fetchOne($cond = null, $sort = null)
 * @method static Tariff      fetchObject($cond = null, $sort = null)
 */
class Tariff extends Model
{
    const BASE = 'base';
    const ADVANCED = 'advanced';
    const EXPERT = 'expert';
}
