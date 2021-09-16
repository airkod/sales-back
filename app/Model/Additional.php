<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Additional
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property string $advertisingDescription
 * @property string $webinar
 * @property string $file
 *
 * @property boolean $enabled
 * @property integer $price
 *
 * @property \App\Model\Course $course
 *
 * @property integer $position
 *
 * @method static Additional[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Additional|null fetchOne($cond = null, $sort = null)
 * @method static Additional      fetchObject($cond = null, $sort = null)
 */
class Additional extends Model
{
}
