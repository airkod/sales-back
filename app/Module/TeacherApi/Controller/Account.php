<?php

declare(strict_types = 1);

namespace App\Module\TeacherApi\Controller;

use App\Model\Teacher;

use Light\Controller;
use Light\Exception;
use Light\Filter\Lowercase;
use Light\Filter\Trim;
use Light\Map;

class Account extends Controller
{
    /**
     * @return array|Map
     * @throws Exception
     */
    public function auth()
    {
        $teacher = Teacher::fetchOne([
            'email' => $this->getParam('email', null, [Trim::class, Lowercase::class]),
            'password' => $this->getParam('password', null, [Trim::class])
        ]);

        if (!$teacher) {
            throw new Exception([], 'Проверьте данные формы авторизации', 400);
        }

        return Map::execute($teacher, [
            'id' => 'id',
            'name' => 'name',
            'email' => 'email'
        ]);
    }

    public function token()
    {
        $teacher = Teacher::fetchOne([
            'id' => $this->getParam('teacherId')
        ]);

        $teacher->token = $this->getParam('token');

        $teacher->save();
    }
}
