<?php

namespace App\Module\Api\Map;

use App\Service\Purchase;
use Light\Map;

class Additional extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'advertisingDescription' => 'advertisingDescription',
            'price' => 'price',

            'file' => function (\App\Model\Additional $additional) {

                return Purchase::isAdditionalAvailable($this->getUserData()['user'], $additional) ?
                    $additional->file :
                    null;
            },

            'webinar' => function (\App\Model\Additional $additional) {

                return Purchase::isAdditionalAvailable($this->getUserData()['user'], $additional) ?
                    $additional->webinar :
                    null;
            },

            'active' => function (\App\Model\Additional $additional) {
                return Purchase::isAdditionalAvailable($this->getUserData()['user'], $additional);
            }
        ];
    }
}
