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
 * @date 28/10/14.10.2014 16:32
 */

namespace Modules\Cart\Controllers;

use Modules\Cart\Tests\Product;

class CartController extends BaseCartController
{
    public function addInternal($uniqueId, $quantity = 1)
    {
        $data = [];
        $this->getCart()->add(new Product(['price' => 10, 'id' => 1]), $quantity, $data);
    }
}
