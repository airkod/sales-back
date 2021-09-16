<?php

namespace App\Module\Admin\Controller;

use Light\Crud\AuthCrud;

class Index extends AuthCrud
{
    /**
     * @throws \Light\Exception\DomainMustBeProvided
     * @throws \Light\Exception\RouterVarMustBeProvided
     */
    public function index()
    {
        $this->redirect(
            $this->getRouter()->assemble([
                'controller' => 'course'
            ])
        );
    }
}
