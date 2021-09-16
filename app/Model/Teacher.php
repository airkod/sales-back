<?php

declare(strict_types = 1);

namespace App\Model;

use Light\Model;

/**
 * @collection Teacher
 *
 * @property string $id
 *
 * @property string $name
 * @property string $image
 *
 * @property string $email
 * @property string $password
 *
 * @property string $token
 *
 * @method static Teacher[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Teacher|null fetchOne($cond = null, $sort = null)
 * @method static Teacher      fetchObject($cond = null, $sort = null)
 */
class Teacher extends Model {}
