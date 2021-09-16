<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection User
 *
 * @property string $id
 *
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 *
 * @property string $openPassword
 *
 * @property integer $bonus
 *
 * @property string $token
 *
 * @property array $availableLessons
 *
 * @property boolean $carteBlanche
 *
 * @method static User[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static User|null fetchOne($cond = null, $sort = null)
 * @method static User      fetchObject($cond = null, $sort = null)
 */
class User extends Model
{

}
