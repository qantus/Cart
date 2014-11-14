<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 28/10/14.10.2014 13:05
 */

namespace Modules\Cart;

use Mindy\Base\Module;

class CartModule extends Module
{
    public function init()
    {
        $this->setComponent('cart', [
            'class' => '\Modules\Cart\Components\Cart'
        ]);
    }

    public function getCart()
    {
        return $this->getComponent('cart');
    }
}
