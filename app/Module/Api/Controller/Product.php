<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use Light\Map;

class Product extends Base
{
    public function index()
    {
        return Map::execute(
            \App\Model\Product::fetchAll(['enabled' => true]),
            [
                'id' => 'id',
                'title' => 'title',
                'image' => 'image',
                'description' => 'description',
                'smallDescription' => 'smallDescription',
                'price' => 'price'
            ]
        );
    }
}
