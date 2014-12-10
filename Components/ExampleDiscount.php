<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 10/12/14 18:36
 */

namespace Modules\Cart\Components;

use Mindy\Base\Mindy;

class ExampleDiscount implements IDiscount
{
    /**
     * Apply discount to CartItem position. If new prices is equal old price - return old price.
     * @param Cart $cart
     * @param CartItem $item
     * @return int|float new price with discount
     */
    public function applyDiscount(Cart $cart, CartItem $item)
    {
        if (Mindy::app()->user->isGuest === false) {
            // Дарим скидку зарегистрированным пользователям
            return $item->getPrice() - 200;
        } else {
            return $item->getPrice();
        }
    }
}
