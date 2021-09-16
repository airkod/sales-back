<?php

declare(strict_types = 1);

namespace App\Model;

use Light\Model;

/**
 * @collection Course
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property string $content
 *
 * @method static Course[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Course|null fetchOne($cond = null, $sort = null)
 * @method static Course      fetchObject($cond = null, $sort = null)
 */
class Course extends Model {}
