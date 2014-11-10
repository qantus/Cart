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
 * @date 10/11/14.11.2014 18:45
 */

namespace Modules\Cart\Interfaces;

interface ICartController
{
    public function addInternal($uniqueId, $quantity = 1);
}
