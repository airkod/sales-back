<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use Light\Controller;

class Index extends Controller //Base
{
    public function index()
    {
        return [
            'ping' => 'pong',
        ];
    }
}
