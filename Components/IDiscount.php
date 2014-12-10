<?php
/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 10/12/14 17:50
 */

namespace Modules\Cart\Components;

/**
 * Interface IDiscount
 * @package Modules\Cart\Components
 */
interface IDiscount
{
    /**
     * Apply discount to CartItem position. If new prices is equal old price - return old price.
     * @param Cart $cart
     * @param CartItem $item
     * @return int|float new price with discount
     */
    public function applyDiscount(Cart $cart, CartItem $item);
}
