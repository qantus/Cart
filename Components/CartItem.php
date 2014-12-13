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
    private $_type;
    /**
     * @var
     */
    private $_quantity = 1;
    /**
     * @var array
     */
    private $_data = [];
    /**
     * @var float original calculated product price based on price * quantity with custom data. See $_data.
     */
    private $_price;
    /**
     * @var float price with applied discounts
     */
    private $_discountPrice;

    /**
     * @param $data
     * @return CartItem
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this->fetchPrice();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param $type
     * @return CartItem
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this->fetchPrice();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param $quantity
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = $quantity;
        return $this->fetchPrice();
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * @param ICartItem $object
     * @return $this
     */
    public function setObject(ICartItem $object)
    {
        $this->_object = $object;
        return $this->fetchPrice();
    }

    /**
     * @return \Mindy\Orm\Model|ICartItem
     */
    public function getObject()
    {
        return $this->_object;
    }

    public function recalculate()
    {
        return $this->getObject()->recalculate($this->_quantity, $this->_type, $this->_data);
    }

    /**
     * @return $this
     */
    private function fetchPrice()
    {
        $object = $this->getObject();
        if ($object) {
            $this->_price = $this->recalculate();
        }
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return (float)str_replace(',', '', $this->_discountPrice ? $this->_discountPrice : $this->_price);
    }

    /**
     * @param IDiscount[] $discounts
     */
    public function applyDiscount(Cart $cart, array $discounts)
    {
        foreach ($discounts as $discount) {
            $this->_discountPrice = $discount->applyDiscount($cart, $this);
        }
    }
}
