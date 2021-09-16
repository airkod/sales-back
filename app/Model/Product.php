<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Product
 *
 * @property string $id
 *
 * @property string $title
 * @property string $smallDescription
 * @property string $description
 * @property string $image
 * @property integer $price
 * @property boolean $enabled
 * @property integer $position
 *
 * @method static Product[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Product|null fetchOne($cond = null, $sort = null)
 * @method static Product      fetchObject($cond = null, $sort = null)
 */
class Product extends Model
{
}
