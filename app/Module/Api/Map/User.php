<?php

namespace App\Module\Api\Map;

use App\Model\Tariff;
use App\Service\Purchase;
use Light\Map;

class User extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [

            'id' => 'id',
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'tariff' => function (\App\Model\User $user) {

                $tariff = Purchase::getUserTariff($user);

                if (!$tariff && !$user->carteBlanche) {
                    return null;
                }

                if ($user->carteBlanche) {

                    $tariff = Tariff::fetchOne([
                        'type' => Tariff::EXPERT
                    ]);
                }

                return Map::execute($tariff, [
                    'type' => 'type',
                    'title' => 'title',
                    'price' => 'price'
                ]);
            },

            'bonus' => function (\App\Model\User $user) {
                return $user->bonus ?? 0;
            },
        ];
    }
}
