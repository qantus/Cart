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
 * @date 16/11/14.11.2014 13:07
 */

namespace Modules\Cart\Components;

use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Modules\Cart\Interfaces\ICartItem;

class CartItem
{
    use Configurator, Accessors;

    /**
     * @var \Mindy\Orm\Model|\Modules\Cart\Interfaces\ICartItem
     */
    private $_object;
    /**
     * @var string weight type
     */
    public $type;
    /**
     * @var
     */
    public $quantity = 1;
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param ICartItem $object
     * @return $this
     */
    public function setObject(ICartItem $object)
    {
        $this->_object = $object;
        return $this;
    }

    /**
     * @return \Mindy\Orm\Model|ICartItem
     */
    public function getObject()
    {
        return $this->_object;
    }

    public function getPrice()
    {
        return $this->getObject()->recalculate($this->quantity, $this->type);
    }
}
